<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MedicalInfoController extends Controller
{
    /**
     * Get user's medical profile
     */
    public function show(Request $request)
    {
        $profile = \App\Models\MedicalProfile::firstOrCreate(
            ['id_user' => $request->user()->id_user],
            [
                'weight_kg' => null,
                'height_cm' => null,
                'drug_allergies' => [],
                'chronic_diseases' => null,
                'has_gerd' => null,
                'blood_type' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }

    /**
     * Update medical profile
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'weight_kg' => 'nullable|numeric|between:20,300',
            'height_cm' => 'nullable|numeric|between:50,300',
            'drug_allergies' => 'nullable|array',
            'chronic_diseases' => 'nullable|string',
            'has_gerd' => 'nullable|boolean',
            'blood_type' => 'nullable|string|in:A,B,AB,O',
        ]);

        $profile = \App\Models\MedicalProfile::updateOrCreate(
            ['id_user' => $request->user()->id_user],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Profil medis berhasil diperbarui',
            'data' => $profile
        ]);
    }
}
