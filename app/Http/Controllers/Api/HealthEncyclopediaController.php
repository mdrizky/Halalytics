<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthEncyclopedia;
use Illuminate\Http\Request;

class HealthEncyclopediaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = HealthEncyclopedia::query();

            // Filter by type if provided
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Filter by alphabet if provided
            if ($request->has('alphabet')) {
                $query->where('alphabet', strtoupper($request->alphabet));
            }

            // Search by title
            if ($request->has('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            $items = $query->orderBy('title', 'asc')->get();

            // If database is empty, return fallback data
            if ($items->isEmpty()) {
                $items = $this->fallbackEncyclopediaItems($request->type);
            }

            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch encyclopedia items: ' . $e->getMessage(),
                'data' => $this->fallbackEncyclopediaItems($request->type) // Always return fallback on error
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $item = HealthEncyclopedia::find($id);

            if (!$item) {
                // Try to find in fallback
                $fallback = collect($this->fallbackEncyclopediaItems())->firstWhere('id', (int)$id);
                if ($fallback) {
                    return response()->json([
                        'success' => true,
                        'data' => $fallback
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function fallbackEncyclopediaItems(?string $type = null): array
    {
        $items = [
            [
                'id' => null,
                'type' => 'obat',
                'title' => 'Paracetamol',
                'content' => 'Obat penurun demam dan pereda nyeri ringan hingga sedang.',
            ],
            [
                'id' => null,
                'type' => 'penyakit',
                'title' => 'Flu',
                'content' => 'Infeksi virus saluran napas atas dengan gejala demam, batuk, dan pilek.',
            ],
            [
                'id' => null,
                'type' => 'hidup_sehat',
                'title' => 'Pola Makan Seimbang',
                'content' => 'Kombinasi karbohidrat, protein, lemak sehat, sayur, dan buah setiap hari.',
            ],
            [
                'id' => null,
                'type' => 'keluarga',
                'title' => 'Pertolongan Pertama Demam Anak',
                'content' => 'Pantau suhu, cukupkan cairan, dan konsultasi dokter bila gejala memberat.',
            ],
        ];

        if ($type && in_array($type, ['obat', 'penyakit', 'hidup_sehat', 'keluarga'], true)) {
            return array_values(array_filter($items, fn ($item) => $item['type'] === $type));
        }

        return $items;
    }
}
