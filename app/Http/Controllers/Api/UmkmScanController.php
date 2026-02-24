<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UmkmProduct;
use App\Models\ActivityModel;
use Illuminate\Http\Request;

class UmkmScanController extends Controller
{
    public function scanQR(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $product = UmkmProduct::where('qr_code_unique_id', $request->qr_code)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau produk tidak aktif'
            ], 404);
        }

        // Record scan
        $product->recordScan();

        // Record Activity
        if (auth('sanctum')->check()) {
            ActivityModel::create([
                'id_user' => auth('sanctum')->id(),
                'aktivitas' => "Men-scan QR UMKM: " . $product->product_name,
                'status' => $product->halal_status
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'product_name' => $product->product_name,
                'product_description' => $product->product_description,
                'product_category' => $product->product_category,
                'umkm_info' => [
                    'name' => $product->umkm_name,
                    'owner' => $product->umkm_owner,
                    'phone' => $product->umkm_phone,
                    'address' => $product->umkm_address,
                ],
                'halal_info' => [
                    'status' => $product->halal_status,
                    'cert_number' => $product->halal_cert_number,
                    'cert_expiry' => $product->halal_cert_expiry,
                    'cert_image' => $product->halal_cert_image ? url($product->halal_cert_image) : null,
                    'is_verified' => $product->is_verified,
                ],
                'nutrition_info' => $product->nutrition_info,
                'ingredients' => $product->ingredients,
            ]
        ]);
    }
}
