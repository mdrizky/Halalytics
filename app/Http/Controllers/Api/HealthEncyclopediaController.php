<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\HealthEncyclopedia;

class HealthEncyclopediaController extends Controller
{
    public function index(Request $request)
    {
        $query = HealthEncyclopedia::query();

        // Optional filter by type (obat, penyakit, hidup_sehat, keluarga)
        if ($request->has('type') && in_array($request->type, ['obat', 'penyakit', 'hidup_sehat', 'keluarga'])) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // Fetch all matching records, sorted by title
        $encyclopedias = $query->orderBy('title', 'asc')->get();

        if ($encyclopedias->isEmpty() && !$request->filled('search')) {
            return response()->json([
                'success' => true,
                'data' => $this->fallbackEncyclopediaItems($request->input('type')),
                'fallback_mode' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $encyclopedias,
            'fallback_mode' => false,
        ]);
    }

    public function show($id)
    {
        $encyclopedia = HealthEncyclopedia::find($id);

        if (!$encyclopedia) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $encyclopedia
        ]);
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
