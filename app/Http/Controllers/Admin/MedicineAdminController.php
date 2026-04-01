<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Services\FDAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MedicineAdminController extends Controller
{
    protected $fdaService;

    public function __construct(FDAService $fdaService)
    {
        $this->fdaService = $fdaService;
    }

    public function index(Request $request)
    {
        if (
            !$request->filled('search') &&
            !$request->filled('halal_status') &&
            !$request->filled('source') &&
            Medicine::count() === 0
        ) {
            $this->seedLocalFallbackMedicines();
        }

        $query = Medicine::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('generic_name', 'LIKE', "%{$search}%")
                  ->orWhere('brand_name', 'LIKE', "%{$search}%")
                  ->orWhere('manufacturer', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('halal_status')) {
            $query->where('halal_status', $request->halal_status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $medicines = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => Medicine::count(),
            'halal' => Medicine::where('halal_status', 'halal')->count(),
            'syubhat' => Medicine::where('halal_status', 'syubhat')->count(),
            'from_fda' => Medicine::where('source', 'openfda')->count(),
        ];

        return view('admin.medicine.index', compact('medicines', 'stats'));
    }

    private function seedLocalFallbackMedicines(): void
    {
        $items = [
            ['name' => 'Paracetamol 500mg', 'generic_name' => 'Paracetamol', 'halal_status' => 'halal'],
            ['name' => 'Ibuprofen 200mg', 'generic_name' => 'Ibuprofen', 'halal_status' => 'syubhat'],
            ['name' => 'Cetirizine 10mg', 'generic_name' => 'Cetirizine', 'halal_status' => 'halal'],
            ['name' => 'Amoxicillin 500mg', 'generic_name' => 'Amoxicillin', 'halal_status' => 'syubhat'],
            ['name' => 'Omeprazole 20mg', 'generic_name' => 'Omeprazole', 'halal_status' => 'halal'],
            ['name' => 'Loratadine 10mg', 'generic_name' => 'Loratadine', 'halal_status' => 'halal'],
            ['name' => 'Mefenamic Acid 500mg', 'generic_name' => 'Mefenamic Acid', 'halal_status' => 'syubhat'],
            ['name' => 'Dextromethorphan Syrup', 'generic_name' => 'Dextromethorphan HBr', 'halal_status' => 'halal'],
            ['name' => 'Vitamin B Complex', 'generic_name' => 'Vitamin B1/B6/B12', 'halal_status' => 'halal'],
            ['name' => 'Cough Syrup Plus', 'generic_name' => 'Guaifenesin', 'halal_status' => 'syubhat'],
        ];

        foreach ($items as $item) {
            Medicine::firstOrCreate(
                ['name' => $item['name']],
                [
                    'generic_name' => $item['generic_name'],
                    'brand_name' => 'Fallback',
                    'source' => 'fallback_seed',
                    'halal_status' => $item['halal_status'],
                    'is_verified_by_admin' => false,
                    'active' => true,
                ]
            );
        }
    }

    public function searchExternal(Request $request)
    {
        $request->validate(['query' => 'required|string|min:2']);

        $query = $request->input('query');
        $results = [];

        try {
            $fdaResult = $this->fdaService->search($query);
            if ($fdaResult['found'] ?? false) {
                foreach ($fdaResult['results'] ?? [] as $item) {
                    $saved = $this->fdaService->importOrUpdate($item, $query);
                    $results[] = $saved ? $saved->fresh() : $item;
                }
            }
        } catch (\Exception $e) {
            Log::error('OpenFDA search error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'query' => $query
        ]);
    }

    public function importFromFda(Request $request)
    {
        $request->validate(['drug_name' => 'required|string']);

        try {
            $fdaResult = $this->fdaService->search($request->drug_name);
            
            if (!($fdaResult['found'] ?? false)) {
                return back()->with('error', 'Obat tidak ditemukan di OpenFDA.');
            }

            $medicine = null;
            foreach ($fdaResult['results'] ?? [] as $item) {
                $medicine = $this->fdaService->importOrUpdate($item, $request->drug_name);
                if ($medicine) {
                    break;
                }
            }

            if ($medicine) {
                return back()->with('success', "Obat '{$medicine->name}' berhasil diimport dari OpenFDA!");
            }

            return back()->with('error', 'Gagal mengimport data obat.');
        } catch (\Exception $e) {
            Log::error('FDA import error: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Seed common medicines from OpenFDA for initial data
     */
    public function seedCommonMedicines()
    {
        $commonDrugs = [
            'Paracetamol', 'Ibuprofen', 'Amoxicillin', 'Cetirizine',
            'Omeprazole', 'Metformin', 'Aspirin', 'Diclofenac',
            'Ranitidine', 'Dexamethasone', 'Chlorpheniramine', 'Loperamide',
            'Mefenamic Acid', 'Doxycycline', 'Simvastatin', 'Amlodipine',
            'Captopril', 'Ciprofloxacin', 'Loratadine', 'Vitamin C'
        ];

        $imported = 0;
        $errors = [];

        foreach ($commonDrugs as $drug) {
            try {
                // Skip if already exists
                if (Medicine::where('name', 'LIKE', "%{$drug}%")->exists()) {
                    continue;
                }

                $fdaResult = $this->fdaService->search($drug);
                if ($fdaResult['found'] ?? false) {
                    foreach ($fdaResult['results'] ?? [] as $item) {
                        $medicine = $this->fdaService->importOrUpdate($item, $drug);
                        if ($medicine) {
                            $imported++;
                            break;
                        }
                    }
                }
                
                // Rate limiting to avoid API throttle
                usleep(300000); // 300ms delay
            } catch (\Exception $e) {
                $errors[] = "{$drug}: {$e->getMessage()}";
                Log::error("Seed medicine error for {$drug}: " . $e->getMessage());
            }
        }

        $message = "Berhasil import {$imported} obat dari OpenFDA.";
        if (!empty($errors)) {
            $message .= ' (' . count($errors) . ' errors)';
        }

        return back()->with('success', $message);
    }
}
