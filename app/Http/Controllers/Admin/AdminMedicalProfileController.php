<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalProfile;
use App\Models\User;
use Illuminate\Http\Request;

class AdminMedicalProfileController extends Controller
{
    /**
     * Display a listing of user medical profiles.
     */
    public function index()
    {
        $profiles = MedicalProfile::with('user')->paginate(20);
        return response()->json([
            'success' => true,
            'data' => $profiles
        ]);
    }

    /**
     * Display the specified user medical profile.
     */
    public function show($id)
    {
        $profile = MedicalProfile::with('user')->where('id_user', $id)->first();
        if (!$profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }

    /**
     * Update the specified user medical profile.
     */
    public function update(Request $request, $id)
    {
        $profile = MedicalProfile::where('id_user', $id)->first();
        if (!$profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found'], 404);
        }

        $validated = $request->validate([
            'weight_kg' => 'nullable|numeric',
            'height_cm' => 'nullable|numeric',
            'blood_type' => 'nullable|string',
            'chronic_diseases' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);

        $profile->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Medical profile updated successfully',
            'data' => $profile
        ]);
    }
}
