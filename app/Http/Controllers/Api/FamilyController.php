<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FamilyProfile;
use App\Services\Health\NutritionalThresholdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FamilyController extends Controller
{
    protected $thresholdService;

    public function __construct(NutritionalThresholdService $thresholdService)
    {
        $this->thresholdService = $thresholdService;
    }

    public function index()
    {
        $family = FamilyProfile::where('user_id', Auth::id())->get();
        return response()->json([
            'success' => true,
            'message' => 'Family profiles retrieved successfully',
            'data' => $family
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'nullable|string',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string',
            'weight_kg' => 'nullable|numeric',
            'height_cm' => 'nullable|numeric',
            'activity_level' => 'nullable|string|in:sedentary,light,moderate,active,very_active',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Calculate thresholds for family member
        $thresholds = $this->thresholdService->calculateThresholds([
            'weight_kg' => $request->weight_kg ?? 0,
            'height_cm' => $request->height_cm ?? 0,
            'age' => $request->age ?? 25,
            'gender' => $request->gender ?? 'male',
            'activity_level' => $request->activity_level ?? 'sedentary',
            'conditions' => $request->medical_history ?? '',
        ]);

        $data = array_merge($data, [
            'daily_calories_target' => $thresholds['calories'],
            'daily_sugar_limit_g' => $thresholds['sugar_g'],
            'daily_sodium_limit_mg' => $thresholds['sodium_mg'],
            'daily_fat_limit_g' => $thresholds['fat_g'],
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('family_profiles', 'public');
        }

        $profile = FamilyProfile::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Family profile created successfully',
            'data' => $profile
        ]);
    }

    public function update(Request $request, $id)
    {
        $profile = FamilyProfile::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'relationship' => 'nullable|string',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string',
            'weight_kg' => 'nullable|numeric',
            'height_cm' => 'nullable|numeric',
            'activity_level' => 'nullable|string|in:sedentary,light,moderate,active,very_active',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        // Re-calculate thresholds if relevant data changed
        if ($request->hasAny(['weight_kg', 'height_cm', 'age', 'gender', 'activity_level', 'medical_history'])) {
            $thresholds = $this->thresholdService->calculateThresholds([
                'weight_kg' => $request->weight_kg ?? $profile->weight_kg,
                'height_cm' => $request->height_cm ?? $profile->height_cm,
                'age' => $request->age ?? $profile->age,
                'gender' => $request->gender ?? $profile->gender,
                'activity_level' => $request->activity_level ?? $profile->activity_level,
                'conditions' => $request->medical_history ?? $profile->medical_history,
            ]);

            $data = array_merge($data, [
                'daily_calories_target' => $thresholds['calories'],
                'daily_sugar_limit_g' => $thresholds['sugar_g'],
                'daily_sodium_limit_mg' => $thresholds['sodium_mg'],
                'daily_fat_limit_g' => $thresholds['fat_g'],
            ]);
        }

        if ($request->hasFile('image')) {
            if ($profile->image_path) {
                Storage::disk('public')->delete($profile->image_path);
            }
            $data['image_path'] = $request->file('image')->store('family_profiles', 'public');
        }

        $profile->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Family profile updated successfully',
            'data' => $profile
        ]);
    }

    public function destroy($id)
    {
        $profile = FamilyProfile::where('user_id', Auth::id())->findOrFail($id);
        
        if ($profile->image_path) {
            Storage::disk('public')->delete($profile->image_path);
        }
        
        $profile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Family profile deleted successfully'
        ]);
    }
}
