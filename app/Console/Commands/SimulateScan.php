<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\ScanHistory;
use App\Models\ScanModel;
use App\Services\FirebaseRealtimeService;

class SimulateScan extends Command
{
    protected $signature = 'scan:simulate {barcode?} {user_id=2}';
    protected $description = 'Simulate a real-time product scan event';

    public function handle(FirebaseRealtimeService $firebaseService)
    {
        $barcode = $this->argument('barcode') ?: '089686010384'; // Indomie default
        $userId = $this->argument('user_id');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $product = ProductModel::where('barcode', $barcode)->first();
        if (!$product) {
            $this->error("Product with barcode {$barcode} not found in local database.");
            return 1;
        }

        $this->info("Simulating scan for product: {$product->nama_product} by user: {$user->username}");

        // 1. Create Scan History
        $scanHistory = ScanHistory::create([
            'user_id' => $user->id_user,
            'scannable_type' => 'App\Models\ProductModel',
            'scannable_id' => $product->id_product,
            'product_name' => $product->nama_product,
            'product_image' => $product->image,
            'barcode' => $product->barcode,
            'halal_status' => $product->status,
            'scan_method' => 'barcode',
            'source' => 'local',
            'created_at' => now()
        ]);

        // 2. Create Legacy Scan
        ScanModel::create([
            'user_id' => $user->id_user,
            'product_id' => $product->id_product,
            'nama_produk' => $product->nama_product,
            'barcode' => $product->barcode,
            'kategori' => 'Live Demo',
            'status_halal' => $product->status,
            'status_kesehatan' => 'sehat',
            'tanggal_scan' => now(),
        ]);

        // 3. Sync to Firebase (REAL-TIME!)
        $this->info("Syncing to Firebase Realtime Database...");
        $firebaseService->syncScanHistory($scanHistory);

        $this->info("SUCCESS! The app history and Admin dashboard will now show this new scan.");
        return 0;
    }
}
