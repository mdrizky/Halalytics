<?php

namespace App\Http\Controllers;

use App\Models\OCRProduct;
use App\Models\ScanHistory;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OCRController extends Controller
{
    public function index()
    {
        if (request()->wantsJson() || request()->is('api/*')) {
            return $this->listByStatuses(['pending', 'pending_admin_review']);
        }

        return view('admin.ocr.index');
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'step' => 'required|in:front,back,processing',
                'user_id' => 'nullable|exists:users,id_user',
                'product_name' => 'nullable|string|max:255',
                'brand' => 'nullable|string|max:255',
                'ingredients_text' => 'nullable|string',
            ]);

            $userId = $request->input('user_id') ?: Auth::user()?->id_user;
            if (!$userId) {
                throw new \RuntimeException('User tidak ditemukan untuk proses OCR.');
            }

            $user = User::findOrFail($userId);
            $image = $request->file('image');
            $step = $request->input('step');
            $filename = 'ocr_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('ocr-images', $filename, 'public');

            $ocrResult = $this->getMockOCRResult($request->input('ingredients_text'));
            $productName = $request->input('product_name') ?: $this->extractProductName($ocrResult['text']);

            $ocrProduct = OCRProduct::query()->updateOrCreate(
                [
                    'user_id' => $user->id_user,
                    'ocr_text_hash' => md5(Str::lower($productName . '|' . ($ocrResult['text'] ?? ''))),
                ],
                [
                    'product_name' => $productName,
                    'brand' => $request->input('brand'),
                    'ingredients_raw' => $ocrResult['text'] ?? '',
                    'ingredients_parsed' => $ocrResult['ingredients'] ?? [],
                    'confidence_level' => $ocrResult['confidence'] ?? 0,
                    'status' => 'pending_admin_review',
                    'source' => 'ocr_web',
                    'front_image_path' => $step === 'front'
                        ? $path
                        : (optional(OCRProduct::query()->where('user_id', $user->id_user)->where('ocr_text_hash', md5(Str::lower($productName . '|' . ($ocrResult['text'] ?? ''))))->first())->front_image_path),
                    'back_image_path' => $step === 'back'
                        ? $path
                        : (optional(OCRProduct::query()->where('user_id', $user->id_user)->where('ocr_text_hash', md5(Str::lower($productName . '|' . ($ocrResult['text'] ?? ''))))->first())->back_image_path),
                    'ai_analysis' => [
                        'step' => $step,
                        'uploaded_via' => 'web_admin',
                    ],
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Gambar OCR berhasil diproses.',
                'data' => $this->transformProduct($ocrProduct->fresh('user')),
            ]);
        } catch (\Throwable $throwable) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR: ' . $throwable->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function getPendingProducts()
    {
        return $this->listByStatuses(['pending', 'pending_admin_review']);
    }

    public function getApprovedProducts()
    {
        return $this->listByStatuses(['approved']);
    }

    public function getRejectedProducts()
    {
        return $this->listByStatuses(['rejected']);
    }

    public function show($id)
    {
        $product = OCRProduct::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail OCR berhasil dimuat.',
            'data' => $this->transformProduct($product),
        ]);
    }

    public function approve(Request $request, $id)
    {
        try {
            $product = OCRProduct::findOrFail($id);
            $product->update([
                'status' => 'approved',
                'verified_by' => Auth::user()?->id_user,
                'verified_at' => now(),
                'ai_analysis' => $this->mergeAiAnalysis($product->ai_analysis, [
                    'admin_review' => [
                        'decision' => 'approved',
                        'note' => $request->input('notes'),
                        'reviewed_at' => now()->toIso8601String(),
                    ],
                ]),
            ]);

            ScanHistory::query()->firstOrCreate(
                [
                    'user_id' => $product->user_id,
                    'scannable_type' => OCRProduct::class,
                    'scannable_id' => $product->id,
                ],
                [
                    'product_name' => $product->product_name ?: 'Produk OCR',
                    'product_image' => $product->front_image_url,
                    'barcode' => null,
                    'halal_status' => $product->halal_status ?: 'unknown',
                    'scan_method' => 'photo',
                    'source' => 'local',
                    'confidence_score' => $product->confidence_level ? (int) round((float) $product->confidence_level) : null,
                    'nutrition_snapshot' => $product->ai_analysis,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Produk OCR berhasil disetujui.',
                'data' => $this->transformProduct($product->fresh('user')),
            ]);
        } catch (\Throwable $throwable) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui produk OCR: ' . $throwable->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'nullable|string|max:1000',
            ]);

            $product = OCRProduct::findOrFail($id);
            $product->update([
                'status' => 'rejected',
                'verified_by' => Auth::user()?->id_user,
                'verified_at' => now(),
                'ai_analysis' => $this->mergeAiAnalysis($product->ai_analysis, [
                    'admin_review' => [
                        'decision' => 'rejected',
                        'reason' => $request->input('reason'),
                        'reviewed_at' => now()->toIso8601String(),
                    ],
                ]),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk OCR berhasil ditolak.',
                'data' => $this->transformProduct($product->fresh('user')),
            ]);
        } catch (\Throwable $throwable) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak produk OCR: ' . $throwable->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:ocr_products,id',
        ]);

        foreach ($request->input('ids', []) as $id) {
            $this->approve(new Request(['notes' => 'Bulk approved by admin']), $id);
        }

        return response()->json([
            'success' => true,
            'message' => count($request->input('ids', [])) . ' produk OCR berhasil disetujui.',
            'data' => null,
        ]);
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:ocr_products,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        foreach ($request->input('ids', []) as $id) {
            $this->reject(new Request(['reason' => $request->input('reason')]), $id);
        }

        return response()->json([
            'success' => true,
            'message' => count($request->input('ids', [])) . ' produk OCR berhasil ditolak.',
            'data' => null,
        ]);
    }

    public function export()
    {
        $products = OCRProduct::with('user')->latest()->get()->map(fn (OCRProduct $product) => $this->transformProduct($product));

        $csvData = "ID,User,Produk,Brand,Status,Confidence,Created At\n";
        foreach ($products as $product) {
            $csvData .= implode(',', [
                $product['id'],
                '"' . str_replace('"', '""', (string) data_get($product, 'user.username', 'unknown')) . '"',
                '"' . str_replace('"', '""', (string) ($product['product_name'] ?? '')) . '"',
                '"' . str_replace('"', '""', (string) ($product['brand'] ?? '')) . '"',
                $product['status'],
                $product['confidence_level'] ?? 0,
                $product['created_at'] ?? '',
            ]) . "\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ocr_products_export_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function statistics()
    {
        $pending = OCRProduct::query()->pending()->count();
        $approved = OCRProduct::query()->where('status', 'approved')->count();
        $rejected = OCRProduct::query()->where('status', 'rejected')->count();

        $stats = [
            'total' => OCRProduct::count(),
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'today_total' => OCRProduct::whereDate('created_at', today())->count(),
            'today_approved' => OCRProduct::where('status', 'approved')->whereDate('verified_at', today())->count(),
            'today_rejected' => OCRProduct::where('status', 'rejected')->whereDate('verified_at', today())->count(),
            'processing_accuracy' => $this->calculateAccuracy(),
            'total_scans' => OCRProduct::count(),
            'pending_review' => $pending,
            'approved_today' => OCRProduct::where('status', 'approved')->whereDate('verified_at', today())->count(),
            'rejected_today' => OCRProduct::where('status', 'rejected')->whereDate('verified_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Statistik OCR berhasil dimuat.',
            'data' => $stats,
        ]);
    }

    private function listByStatuses(array $statuses)
    {
        $products = OCRProduct::with('user')
            ->whereIn('status', $statuses)
            ->orderByDesc('verified_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Daftar OCR berhasil dimuat.',
            'data' => $this->transformPaginator($products),
        ]);
    }

    private function transformPaginator(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'data' => $paginator->getCollection()->map(fn (OCRProduct $product) => $this->transformProduct($product))->values(),
        ];
    }

    private function transformProduct(OCRProduct $product): array
    {
        $ingredients = $this->extractIngredients($product);
        $adminReview = data_get($product->ai_analysis, 'admin_review', []);

        return [
            'id' => $product->id,
            'product_name' => $product->product_name ?: 'Produk OCR',
            'brand' => $product->brand,
            'country' => $product->country,
            'barcode' => null,
            'status' => $product->status,
            'status_label' => $this->statusLabel($product->status),
            'processing_step' => $product->processing_step_label,
            'front_image_url' => $product->front_image_url,
            'back_image_url' => $product->back_image_url,
            'front_image_path' => $product->front_image_path,
            'back_image_path' => $product->back_image_path,
            'ingredients' => $ingredients,
            'ingredients_count' => count($ingredients),
            'extracted_text' => $product->ingredients_raw,
            'confidence_level' => $product->confidence_level,
            'confidence_score' => $product->confidence_level,
            'halal_status' => $product->halal_status,
            'created_at' => optional($product->created_at)->toIso8601String(),
            'verified_at' => optional($product->verified_at)->toIso8601String(),
            'admin_note' => data_get($adminReview, 'note') ?: data_get($adminReview, 'reason'),
            'user' => [
                'id' => $product->user?->id_user,
                'username' => $product->user?->username,
                'full_name' => $product->user?->full_name,
            ],
        ];
    }

    private function extractIngredients(OCRProduct $product): array
    {
        if (is_array($product->ingredients_parsed) && count($product->ingredients_parsed) > 0) {
            return array_values(array_filter($product->ingredients_parsed));
        }

        return collect(preg_split('/[,;\n]+/', (string) $product->ingredients_raw))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->take(30)
            ->values()
            ->all();
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'pending', 'pending_admin_review' => 'Menunggu Review',
            default => Str::headline($status),
        };
    }

    private function mergeAiAnalysis($current, array $extra): array
    {
        $current = is_array($current) ? $current : [];

        return array_replace_recursive($current, $extra);
    }

    private function extractProductName(?string $text): string
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $text);

        return trim((string) ($lines[0] ?? 'Produk OCR'));
    }

    private function calculateAccuracy(): float
    {
        $reviewed = OCRProduct::query()->whereIn('status', ['approved', 'rejected'])->count();
        $approved = OCRProduct::query()->where('status', 'approved')->count();

        return $reviewed > 0 ? round(($approved / $reviewed) * 100, 2) : 0.0;
    }

    private function getMockOCRResult(?string $ingredientsText = null): array
    {
        $text = $ingredientsText ?: 'Gula, garam, tepung terigu, minyak nabati, perisa alami, penguat rasa.';

        return [
            'text' => $text,
            'ingredients' => collect(preg_split('/[,;\n]+/', $text))
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->values()
                ->all(),
            'confidence' => 92.5,
        ];
    }
}
