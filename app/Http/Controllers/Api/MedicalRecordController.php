<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('user_id'); // Assuming passed from mobile
        
        $records = MedicalRecord::where('id_user', $userId)
            ->where('is_archived', false)
            ->orderBy('record_date', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $records
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required',
            'record_type' => 'required',
            'record_date' => 'required|date',
            'title' => 'required',
            'image_base64' => 'nullable'
        ]);

        $fileName = null;
        if ($request->has('image_base64') && !empty($request->image_base64)) {
            $imageData = base64_decode($request->image_base64);
            $fileName = 'medical_records/' . time() . '.jpg';
            Storage::disk('public')->put($fileName, $imageData);
        }

        $record = MedicalRecord::create([
            'id_user' => $request->id_user,
            'record_type' => $request->record_type,
            'record_date' => $request->record_date,
            'title' => $request->title,
            'description' => $request->description,
            'hospital_name' => $request->hospital_name,
            'doctor_name' => $request->doctor_name,
            'file_path' => $fileName,
            'tags' => $request->tags ? json_encode($request->tags) : null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $record
        ], 201);
    }
}
