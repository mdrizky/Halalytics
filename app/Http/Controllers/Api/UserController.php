<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Models\DailyIntake;
use App\Models\ScanHistory;
use App\Models\ScanModel; // Legacy
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // SCAN PRODUK & HEALTH CHECK
    public function scanProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Search product in approved list
        $product = ProductModel::where('barcode', $request->barcode)
            ->where('approval_status', 'approved')
            ->first();

        if (!$product) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Product not found or not approved yet',
            ], 404);
        }

        $user = $request->user();
        $warningMessage = null;
        $warningTriggered = false;

        // --- HEALTH ALGORITHM ---
        
        // 1. Diabetes Check
        if ($user->has_diabetes && $product->sugar_g > 15) {
            $warningMessage = "⚠️ BAHAYA: Produk ini mengandung {$product->sugar_g}g gula. Tidak direkomendasikan untuk Diabetes!";
            $warningTriggered = true;
        }

        // 2. Caffeine Check (>150mg = High)
        if ($product->caffeine_mg > 150) {
            $caffeineWarning = "⚠️ PERINGATAN: Kafein tinggi ({$product->caffeine_mg}mg). Pastikan minum air lebih banyak!";
            $warningMessage = $warningMessage ? $warningMessage . "\n" . $caffeineWarning : $caffeineWarning;
            $warningTriggered = true;
        }

        // --- DAILY INTAKE UPDATE ---
        $today = now()->format('Y-m-d');
        
        $dailyIntake = DailyIntake::firstOrCreate(
            ['user_id' => $user->id_user, 'intake_date' => $today],
            [
                'total_water_ml' => 0, 
                'total_caffeine_mg' => 0, 
                'total_sugar_g' => 0, 
                'total_calories' => 0
            ]
        );

        $dailyIntake->increment('total_water_ml', $product->volume_ml ?? 0);
        $dailyIntake->increment('total_caffeine_mg', $product->caffeine_mg);
        $dailyIntake->increment('total_sugar_g', $product->sugar_g);
        $dailyIntake->increment('total_calories', $product->calories);

        // --- RECORD HISTORY ---
        // Using new scan_histories table (ScanHistory model)
        ScanHistory::create([
            'user_id' => $user->id_user,
            // Polymorphic relation
            'scannable_type' => 'App\Models\ProductModel',
            'scannable_id' => $product->id_product,
            
            'product_name' => $product->nama_product ?: $product->product_name, // Handle naming variation
            'barcode' => $product->barcode,
            'halal_status' => $product->status, // halal/haram/syubhat
            'source' => $product->source ?? 'local',
            'health_warning_triggered' => $warningTriggered,
            'warning_message' => $warningMessage,
        ]);

        return response()->json([
            'status' => 'success',
            'product' => $product,
            'health_warning' => $warningMessage,
            'daily_intake' => $dailyIntake,
        ]);
    }

    // GET DAILY PROGRESS & TARGETS
    public function getDailyIntake(Request $request)
    {
        $user = $request->user();
        $today = now()->format('Y-m-d');

        $dailyIntake = DailyIntake::where('user_id', $user->id_user)
            ->where('intake_date', $today)
            ->first();

        // Prepare default zero data if no record found
        $intakeData = $dailyIntake ? [
            'total_water_ml' => $dailyIntake->total_water_ml,
            'total_caffeine_mg' => $dailyIntake->total_caffeine_mg,
            'total_sugar_g' => $dailyIntake->total_sugar_g,
            'total_calories' => $dailyIntake->total_calories,
        ] : [
            'total_water_ml' => 0,
            'total_caffeine_mg' => 0,
            'total_sugar_g' => 0,
            'total_calories' => 0,
        ];

        // CALCULATE TARGETS
        // Water: Weight based (30ml per kg) or default 2000ml
        $waterTarget = $user->weight_kg ? ($user->weight_kg * 30) : 2000;
        $caffeineLimit = 400; // FDA Standard

        return response()->json([
            'daily_intake' => $intakeData,
            'targets' => [
                'water_target_ml' => $waterTarget,
                'caffeine_limit_mg' => $caffeineLimit,
            ],
            'progress' => [
                'water_percentage' => min(100, ($intakeData['total_water_ml'] / $waterTarget) * 100),
                'caffeine_percentage' => min(100, ($intakeData['total_caffeine_mg'] / $caffeineLimit) * 100),
            ],
        ]);
    }
}
