<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UmkmProduct;

class UmkmSeeder extends Seeder
{
    public function run()
    {
        UmkmProduct::create([
            'umkm_name' => 'Bakso Pak Kumis',
            'umkm_owner' => 'Pak Kumis',
            'umkm_phone' => '08123456789',
            'umkm_address' => 'Jl. Merdeka No. 10',
            'product_name' => 'Bakso Halal Spesial',
            'product_description' => 'Bakso sapi asli dengan bumbu rempah pilihan.',
            'product_category' => 'Makanan Berat',
            'halal_status' => 'halal_mui',
            'is_verified' => true,
            'qr_code_unique_id' => 'UMKM-BAKSO-001'
        ]);

        UmkmProduct::create([
            'umkm_name' => 'Tempe Barokah',
            'umkm_owner' => 'Bu Barokah',
            'umkm_phone' => '08987654321',
            'umkm_address' => 'Jl. Barokah No. 5',
            'product_name' => 'Kripik Tempe Renyah',
            'product_description' => 'Kripik tempe renyah dengan varian rasa original.',
            'product_category' => 'Cemilan',
            'halal_status' => 'self_declared',
            'is_verified' => true,
            'qr_code_unique_id' => 'UMKM-TEMPE-002'
        ]);
    }
}
