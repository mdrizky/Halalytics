<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductRequest;
use App\Models\User;
use App\Models\Notification;
use App\Services\FirebaseRealtimeService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductRequestController extends Controller
{
    public function __construct(private FirebaseRealtimeService $firebaseRealtimeService)
    {
    }

    public function store(Request $request)
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'barcode' => 'required|string',
            'image_front' => 'required|image|max:5120', // Max 5MB
            'image_back' => 'required|image|max:5120',
            'ocr_text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'details' => $validator->errors()
            ], 422);
        }

        try {
            // Upload Images
            $frontPath = $request->file('image_front')->store('product_requests/front', 'public');
            $backPath = $request->file('image_back')->store('product_requests/back', 'public');

            $productRequest = ProductRequest::create([
                'user_id' => $request->user()->id_user,
                'barcode' => $request->barcode,
                'product_name' => $request->product_name ?? 'Unknown Product',
                'image_front' => $frontPath,
                'image_back' => $backPath,
                'ocr_text' => $request->ocr_text,
                'status' => 'pending'
            ]);

            // Notify Admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $notification = Notification::create([
                    'user_id' => $admin->id_user,
                    'title' => 'Permintaan Produk Baru',
                    'message' => "Pengguna {$request->user()->full_name} meminta verifikasi untuk produk: {$productRequest->product_name}",
                    'type' => 'verification',
                    'action_type' => 'view_request',
                    'action_value' => (string)$productRequest->id
                ]);

                $this->firebaseRealtimeService->syncNotification($notification);
            }

            return response()->json([
                'success' => true,
                'message' => 'Request submitted successfully',
                'data' => $productRequest
            ], 201);
        } catch (\Exception $e) {
            Log::error('Product request submit failed', [
                'user_id' => optional($request->user())->id_user,
                'barcode' => $request->input('barcode'),
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit request: ' . $e->getMessage()
            ], 500);
        }
    }
}
