<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FamilyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FamilyController extends Controller
{
    /**
     * Display a listing of family profiles for the authenticated user.
     */
    public function index()
    {
        $profiles = FamilyProfile::where('user_id', Auth::id())->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar profil keluarga berhasil diambil',
            'data' => $profiles
        ]);
    }

    /**
     * Store a newly created family profile.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'relationship' => 'nullable|string|max:100',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female,other',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Limit check: max 5 profiles
        $count = FamilyProfile::where('user_id', Auth::id())->count();
        if ($count >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Batas maksimum 5 profil keluarga telah tercapai'
            ], 403);
        }

        $data = $request->only(['name', 'relationship', 'age', 'gender', 'allergies', 'medical_history']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/family_images', $filename);
            $data['image_path'] = '/storage/family_images/' . $filename;
        }

        $profile = FamilyProfile::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil keluarga berhasil dibuat',
            'data' => $profile
        ], 201);
    }

    /**
     * Update the specified family profile.
     */
    public function update(Request $request, $id)
    {
        $profile = FamilyProfile::where('user_id', Auth::id())->find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:100',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female,other',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'relationship', 'age', 'gender', 'allergies', 'medical_history']);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($profile->image_path) {
                $oldPath = str_replace('/storage/', 'public/', $profile->image_path);
                Storage::delete($oldPath);
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/family_images', $filename);
            $data['image_path'] = '/storage/family_images/' . $filename;
        }

        $profile->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil keluarga berhasil diperbarui',
            'data' => $profile
        ]);
    }

    /**
     * Remove the specified family profile.
     */
    public function destroy($id)
    {
        $profile = FamilyProfile::where('user_id', Auth::id())->find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil tidak ditemukan'
            ], 404);
        }

        // Delete image
        if ($profile->image_path) {
            $oldPath = str_replace('/storage/', 'public/', $profile->image_path);
            Storage::delete($oldPath);
        }

        $profile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Profil keluarga berhasil dihapus'
        ]);
    }
}
