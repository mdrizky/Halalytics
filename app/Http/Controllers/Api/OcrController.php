<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HaramIngredient;
use App\Models\OcrScanHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OcrController extends Controller
{
    public function syncIngredients(Request $request)
    {
        $updatedAfter = $request->input('updated_after');

        $ingredients = $updatedAfter
            ? HaramIngredient::query()
                ->where('is_active', true)
                ->where('updated_at', '>', $updatedAfter)
                ->orderBy('updated_at')
                ->get()
            : Cache::remember('haram_ingredients_all', 3600, function () {
                return HaramIngredient::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();
            });

        return $this->successResponse(
            $ingredients->map(fn (HaramIngredient $ingredient) => [
                'id' => $ingredient->id,
                'name' => $ingredient->name,
                'aliases' => $ingredient->aliases ?? [],
                'category' => $ingredient->category,
                'severity' => (int) $ingredient->severity,
                'description' => $ingredient->description,
                'is_active' => (bool) $ingredient->is_active,
                'updated_at' => optional($ingredient->updated_at)->toISOString(),
            ]),
            'Data bahan haram berhasil disinkronkan.'
        );
    }

    public function scanResult(Request $request)
    {
        $validated = $request->validate([
            'product_name'   => 'nullable|string|max:255',
            'raw_text'       => 'required|string',
            'detected_haram' => 'nullable|array',
            'severity'       => 'nullable|integer|min:0|max:3',
        ]);

        $scan = OcrScanHistory::create([
            'user_id'        => $request->user()->id_user,
            'product_name'   => $validated['product_name'] ?? null,
            'raw_text'       => $validated['raw_text'],
            'detected_haram' => $validated['detected_haram'] ?? [],
            'severity'       => $validated['severity'] ?? null,
            'scanned_at'     => now(),
        ]);

        return $this->successResponse([
            'scan' => $scan,
            'contains_haram' => ! empty($validated['detected_haram'] ?? []),
            'max_severity' => (int) ($validated['severity'] ?? 0),
        ], 'Hasil scan berhasil disimpan.');
    }

    public function history()
    {
        $scans = OcrScanHistory::where('user_id', auth()->id())
            ->orderByDesc('scanned_at')
            ->paginate(20);

        return $this->successResponse($scans, 'Riwayat OCR berhasil diambil.');
    }
}
