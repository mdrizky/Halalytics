<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HaramIngredient;
use App\Models\OcrScanHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OcrController extends Controller
{
    public function syncIngredients(Request $request)
    {
        if ($request->updated_after) {
            $ingredients = HaramIngredient::where('is_active', true)
                ->where('updated_at', '>', $request->updated_after)
                ->get();
        } else {
            $ingredients = Cache::remember('haram_ingredients_all', 3600, function () {
                return HaramIngredient::where('is_active', true)->get();
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Data bahan haram berhasil disinkronkan',
            'data'    => $ingredients,
        ]);
    }

    public function scanResult(Request $request)
    {
        $request->validate([
            'product_name'   => 'nullable|string|max:255',
            'raw_text'       => 'required|string',
            'detected_haram' => 'nullable|array',
            'severity'       => 'nullable|integer|min:0|max:3',
        ]);

        $scan = OcrScanHistory::create([
            'user_id'        => Auth::id(),
            'product_name'   => $request->product_name,
            'raw_text'       => $request->raw_text,
            'detected_haram' => $request->detected_haram,
            'severity'       => $request->severity,
            'scanned_at'     => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hasil scan berhasil disimpan',
            'data'    => $scan,
        ]);
    }

    public function history()
    {
        $scans = OcrScanHistory::where('user_id', Auth::id())
            ->orderByDesc('scanned_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $scans]);
    }
}
