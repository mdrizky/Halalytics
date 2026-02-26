<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\DrugInteraction;
use App\Models\AiQueryLog;
use App\Services\ActivityEventService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DrugInteractionController extends Controller
{
    private $geminiService;
    private $activityEventService;

    public function __construct(GeminiService $geminiService, ActivityEventService $activityEventService)
    {
        $this->geminiService = $geminiService;
        $this->activityEventService = $activityEventService;
    }

    /**
     * Check interaction between two drugs
     */
    public function check(Request $request)
    {
        $request->validate([
            'drug_a_id' => 'nullable|exists:medicines,id_medicine',
            'drug_b_id' => 'nullable|exists:medicines,id_medicine',
            'drug_a_name' => 'nullable|string',
            'drug_b_name' => 'nullable|string'
        ]);

        $drugA = null;
        $drugB = null;

        // Resolve Drug A
        if ($request->drug_a_id) {
            $drugA = Medicine::where('id_medicine', $request->drug_a_id)->first();
        } elseif ($request->drug_a_name) {
            $drugA = Medicine::where('name', 'LIKE', "%{$request->drug_a_name}%")->first() ?: ['name' => $request->drug_a_name, 'generic_name' => $request->drug_a_name];
        }

        // Resolve Drug B
        if ($request->drug_b_id) {
            $drugB = Medicine::where('id_medicine', $request->drug_b_id)->first();
        } elseif ($request->drug_b_name) {
            $drugB = Medicine::where('name', 'LIKE', "%{$request->drug_b_name}%")->first() ?: ['name' => $request->drug_b_name, 'generic_name' => $request->drug_b_name];
        }

        if (!$drugA || !$drugB) {
            return response()->json([
                'success' => false,
                'message' => 'Kedua obat harus ditentukan.'
            ], 422);
        }

        // Check cache/DB for existing interaction if both are in DB
        if (is_object($drugA) && is_object($drugB)) {
            $existing = DrugInteraction::where(function($q) use ($drugA, $drugB) {
                $q->where('medicine_a_id', $drugA->id_medicine)->where('medicine_b_id', $drugB->id_medicine);
            })->orWhere(function($q) use ($drugA, $drugB) {
                $q->where('medicine_a_id', $drugB->id_medicine)->where('medicine_b_id', $drugA->id_medicine);
            })->first();

            if ($existing) {
                return response()->json([
                    'success' => true,
                    'source' => 'database',
                    'data' => $existing
                ]);
            }
        }

        $drugAName = is_object($drugA) ? ($drugA->name ?? $drugA->generic_name ?? null) : ($drugA['name'] ?? null);
        $drugBName = is_object($drugB) ? ($drugB->name ?? $drugB->generic_name ?? null) : ($drugB['name'] ?? null);

        // Primary source: OpenFDA
        try {
            $normalized = $this->checkWithOpenFda($drugAName, $drugBName);
            $source = 'openfda';

            if (!$normalized || $this->isWeakInteractionResult($normalized)) {
                $normalized = $this->checkWithRxNav($drugAName, $drugBName);
                $source = 'rxnav_fallback';
            }

            if (!$normalized) {
                $aiResult = $this->geminiService->checkDrugInteraction(
                    is_object($drugA) ? $drugA->toArray() : (array)$drugA,
                    is_object($drugB) ? $drugB->toArray() : (array)$drugB
                );
                $normalized = [
                    'has_interaction' => (bool)($aiResult['has_interaction'] ?? false),
                    'severity' => $this->normalizeSeverity($aiResult['severity'] ?? null),
                    'description' => $aiResult['description'] ?? 'Interaksi tidak terdeteksi.',
                    'recommendation' => $aiResult['recommendation'] ?? 'Tetap konsultasi dengan dokter/apoteker.',
                    'scientific_basis' => $aiResult['scientific_basis'] ?? null,
                    'sources' => ['ai_fallback'],
                    'disclaimer' => 'Hasil ini hanya referensi edukasi. Ikuti resep dokter/apoteker sebagai acuan utama.',
                ];
                $source = 'ai_fallback';
            }

            // Log query
            AiQueryLog::create([
                'id_user' => Auth::user()?->id_user,
                'query_type' => 'interaction_check',
                'input_data' => ['drug_a' => $drugAName, 'drug_b' => $drugBName],
                'ai_response' => $normalized,
                'processing_time' => null
            ]);

            // Save to DB for future if both were in DB
            if (is_object($drugA) && is_object($drugB)) {
                DrugInteraction::updateOrCreate(
                    [
                        'medicine_a_id' => $drugA->id_medicine,
                        'medicine_b_id' => $drugB->id_medicine,
                    ],
                    [
                        'severity' => $normalized['severity'] ?? 'moderate',
                        'description' => $normalized['description'] ?? 'No interaction found',
                        'recommendation' => $normalized['recommendation'] ?? null,
                        'ai_verified' => true,
                        'verified_at' => now()
                    ]
                );
            }

            $this->activityEventService->logEvent(
                eventType: 'drug_interaction',
                userId: Auth::user()?->id_user,
                username: Auth::user()?->username,
                entityRef: trim(($drugAName ?? '-') . ' vs ' . ($drugBName ?? '-')),
                summary: 'Drug interaction check: ' . ($drugAName ?? '-') . ' x ' . ($drugBName ?? '-'),
                status: 'success',
                payload: [
                    'severity' => $normalized['severity'] ?? 'minor',
                    'has_interaction' => $normalized['has_interaction'] ?? false,
                    'source' => $source,
                ]
            );

            return response()->json([
                'success' => true,
                'source' => $source,
                'data' => $normalized
            ], 200);

        } catch (\Exception $e) {
            Log::error('Drug interaction check failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa interaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function checkWithOpenFda(?string $drugA, ?string $drugB): ?array
    {
        if (!$drugA || !$drugB) {
            return null;
        }

        $query = sprintf(
            'patient.drug.medicinalproduct:"%s"+AND+patient.drug.medicinalproduct:"%s"',
            addslashes($drugA),
            addslashes($drugB)
        );

        $response = Http::timeout(20)->get('https://api.fda.gov/drug/event.json', [
            'search' => $query,
            'limit' => 5,
        ]);

        if (!$response->successful()) {
            return null;
        }

        $json = $response->json();
        $total = (int) data_get($json, 'meta.results.total', 0);
        $results = data_get($json, 'results', []);

        if ($total <= 0 || empty($results)) {
            return null;
        }

        $severity = $this->severityFromOpenFda($results);
        return [
            'has_interaction' => true,
            'severity' => $severity,
            'description' => "OpenFDA menemukan {$total} laporan kejadian terkait kombinasi {$drugA} dan {$drugB}.",
            'recommendation' => $this->recommendationBySeverity($severity),
            'scientific_basis' => 'Signal based on FDA adverse event reports (FAERS).',
            'sources' => ['openfda'],
            'disclaimer' => 'Data kejadian tidak selalu membuktikan sebab-akibat. Konsultasi dokter/apoteker wajib.',
        ];
    }

    private function checkWithRxNav(?string $drugA, ?string $drugB): ?array
    {
        if (!$drugA || !$drugB) {
            return null;
        }

        $rxA = $this->resolveRxcui($drugA);
        $rxB = $this->resolveRxcui($drugB);

        if (!$rxA || !$rxB) {
            return null;
        }

        $response = Http::timeout(20)->get('https://rxnav.nlm.nih.gov/REST/interaction/list.json', [
            'rxcuis' => "{$rxA}+{$rxB}",
        ]);

        if (!$response->successful()) {
            return null;
        }

        $json = $response->json();
        $pairs = data_get($json, 'fullInteractionTypeGroup.0.fullInteractionType.0.interactionPair', []);
        if (empty($pairs)) {
            return [
                'has_interaction' => false,
                'severity' => 'minor',
                'description' => "Tidak ditemukan interaksi signifikan antara {$drugA} dan {$drugB} pada RxNav.",
                'recommendation' => 'Tetap gunakan sesuai resep dan pantau gejala.',
                'scientific_basis' => 'NIH RxNav interaction dataset.',
                'sources' => ['rxnav'],
                'disclaimer' => 'Hasil ini referensi edukasi, bukan pengganti konsultasi klinis.',
            ];
        }

        $description = (string)($pairs[0]['description'] ?? "Ada potensi interaksi {$drugA} dan {$drugB}.");
        $severity = $this->normalizeSeverity($this->extractSeverityFromText($description));

        return [
            'has_interaction' => true,
            'severity' => $severity,
            'description' => $description,
            'recommendation' => $this->recommendationBySeverity($severity),
            'scientific_basis' => 'NIH RxNav interaction dataset.',
            'sources' => ['rxnav'],
            'disclaimer' => 'Hasil ini referensi edukasi, bukan pengganti konsultasi klinis.',
        ];
    }

    private function resolveRxcui(string $drugName): ?string
    {
        $response = Http::timeout(15)->get('https://rxnav.nlm.nih.gov/REST/rxcui.json', [
            'name' => $drugName,
            'search' => 1,
        ]);

        if (!$response->successful()) {
            return null;
        }

        $ids = data_get($response->json(), 'idGroup.rxnormId', []);
        return $ids[0] ?? null;
    }

    private function severityFromOpenFda(array $results): string
    {
        $scores = ['minor' => 0, 'moderate' => 0, 'major' => 0, 'contraindicated' => 0];
        foreach ($results as $row) {
            $reaction = strtolower((string) data_get($row, 'patient.reaction.0.reactionmeddrapt', ''));
            $outcome = (int) data_get($row, 'patient.reaction.0.reactionoutcome', 0);

            if (str_contains($reaction, 'death') || in_array($outcome, [4, 5], true)) {
                $scores['contraindicated']++;
            } elseif (str_contains($reaction, 'hospital') || $outcome === 3) {
                $scores['major']++;
            } elseif (str_contains($reaction, 'serious')) {
                $scores['moderate']++;
            } else {
                $scores['minor']++;
            }
        }

        arsort($scores);
        return array_key_first($scores) ?: 'moderate';
    }

    private function normalizeSeverity(?string $severity): string
    {
        $value = strtolower(trim((string) $severity));
        return match (true) {
            str_contains($value, 'contra') => 'contraindicated',
            str_contains($value, 'major'), str_contains($value, 'severe'), str_contains($value, 'high') => 'major',
            str_contains($value, 'moderate'), str_contains($value, 'medium') => 'moderate',
            default => 'minor',
        };
    }

    private function recommendationBySeverity(string $severity): string
    {
        return match ($severity) {
            'contraindicated' => 'Hindari kombinasi ini. Hubungi dokter segera untuk alternatif obat.',
            'major' => 'Risiko tinggi. Gunakan hanya dengan pengawasan dokter/apoteker.',
            'moderate' => 'Perlu perhatian. Pantau gejala dan diskusikan dengan tenaga kesehatan.',
            default => 'Risiko rendah, tetap ikuti aturan pakai dan pantau kondisi.',
        };
    }

    private function extractSeverityFromText(string $text): string
    {
        $t = strtolower($text);
        if (str_contains($t, 'contraindicated')) return 'contraindicated';
        if (str_contains($t, 'major') || str_contains($t, 'severe')) return 'major';
        if (str_contains($t, 'moderate')) return 'moderate';
        return 'minor';
    }

    private function isWeakInteractionResult(array $data): bool
    {
        return empty($data['description']) || !array_key_exists('has_interaction', $data);
    }

    /**
     * Search drugs in master data
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        if (!$query) return response()->json(['success' => true, 'data' => []]);

        $results = Medicine::where('name', 'LIKE', "%{$query}%")
            ->orWhere('generic_name', 'LIKE', "%{$query}%")
            ->orWhere('brand_name', 'LIKE', "%{$query}%")
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
}
