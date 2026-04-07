<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\ProductModel;
use Illuminate\Support\Str;

class HalalyticsFeatureSeeder extends Seeder
{
    public function run()
    {
        // Removed UMKM Seeding
        $this->seedPendingVerificationProducts();
    }


    /**
     * Seed products that need manual verification (for Smart Verification feature)
     */
    protected function seedPendingVerificationProducts()
    {
        $pendingProducts = [
            [
                'nama_product' => 'Chips Ahoy Cookies',
                'barcode' => '7622210449283',
                'source' => 'open_food_facts',
                'verification_status' => 'needs_review',
                'needs_manual_review' => true,
                'status' => 'syubhat',
                'komposisi' => json_encode(['tepung terigu', 'gula', 'margarin', 'cokelat chips', 'pengemulsi E471', 'perisa vanila']),
                'data_completeness_score' => 75,
                'is_imported_from_off' => true,
                'auto_imported_at' => now(),
            ],
            [
                'nama_product' => 'Haribo Goldbären',
                'barcode' => '4001686301098',
                'source' => 'open_food_facts',
                'verification_status' => 'needs_review',
                'needs_manual_review' => true,
                'status' => 'syubhat',
                'komposisi' => json_encode(['sirup glukosa', 'gula', 'gelatin', 'asam sitrat', 'konsentrat jus buah']),
                'data_completeness_score' => 60,
                'is_imported_from_off' => true,
                'auto_imported_at' => now()->subHours(2),
            ],
            [
                'nama_product' => 'M&M\'s Peanut',
                'barcode' => '5000159304306',
                'source' => 'open_food_facts',
                'verification_status' => 'needs_review',
                'needs_manual_review' => true,
                'status' => 'syubhat',
                'komposisi' => json_encode(['kacang tanah', 'gula', 'kakao', 'susu bubuk', 'pewarna makanan E102', 'E110', 'E129']),
                'data_completeness_score' => 82,
                'is_imported_from_off' => true,
                'auto_imported_at' => now()->subDay(),
            ],
            [
                'nama_product' => 'Pringles Original',
                'barcode' => '5053990101573',
                'source' => 'open_food_facts',
                'verification_status' => 'needs_review',
                'needs_manual_review' => true,
                'status' => 'halal',
                'komposisi' => json_encode(['kentang dehidrasi', 'minyak sayur', 'tepung beras', 'garam', 'maltodextrin']),
                'data_completeness_score' => 90,
                'is_imported_from_off' => true,
                'auto_imported_at' => now()->subDays(2),
            ],
            [
                'nama_product' => 'Oreo Double Stuf',
                'barcode' => '7622210100023',
                'source' => 'open_food_facts',
                'verification_status' => 'needs_review',
                'needs_manual_review' => true,
                'status' => 'syubhat',
                'komposisi' => json_encode(['tepung terigu', 'gula', 'minyak kelapa sawit', 'kakao', 'sirup glukosa', 'pengemulsi lesitin kedelai']),
                'data_completeness_score' => 85,
                'is_imported_from_off' => true,
                'auto_imported_at' => now()->subHours(5),
            ],
        ];

        foreach ($pendingProducts as $data) {
            ProductModel::firstOrCreate(
                ['barcode' => $data['barcode']],
                $data
            );
        }

        $this->command->info('✅ Pending verification products seeded successfully!');
    }
}
