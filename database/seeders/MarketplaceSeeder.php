<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\MarketplaceProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketplaceSeeder extends Seeder
{
    public function run()
    {
        // 1. Health Facilities
        $facilities = [
            [
                'name' => 'RS Medika Halal Jakarta',
                'type' => 'rs',
                'address' => 'Jl. Sudirman No. 12, Jakarta Pusat',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'phone' => '021-1234567',
                'is_verified' => true,
            ],
            [
                'name' => 'Apotek Sehat Barokah',
                'type' => 'apotek',
                'address' => 'Jl. Thamrin No. 45, Jakarta Pusat',
                'latitude' => -6.1892,
                'longitude' => 106.8234,
                'phone' => '021-7654321',
                'is_verified' => true,
            ],
            [
                'name' => 'Klinik Pratama Al-Syifa',
                'type' => 'klinik',
                'address' => 'Jl. Gatot Subroto No. 88, Jakarta Selatan',
                'latitude' => -6.2345,
                'longitude' => 106.8123,
                'phone' => '021-9988776',
                'is_verified' => true,
            ],
        ];

        foreach ($facilities as $facility) {
            Merchant::updateOrCreate(['name' => $facility['name']], $facility);
        }

        // 2. Marketplace Merchants (Stores)
        $merchants = [
            [
                'name' => 'Toko Herbal Al-Hikmah',
                'type' => 'toko_halal',
                'address' => 'Pasar Minggu Kav. 4, Jakarta Selatan',
                'latitude' => -6.2847,
                'longitude' => 106.8444,
                'is_verified' => true,
            ],
            [
                'name' => 'Resto Ayam Penyet Syariah',
                'type' => 'restoran_halal',
                'address' => 'Jl. Margonda No. 10, Depok',
                'latitude' => -6.3731,
                'longitude' => 106.8340,
                'is_verified' => true,
            ],
        ];

        foreach ($merchants as $m) {
            $merchant = Merchant::updateOrCreate(['name' => $m['name']], $m);

            // Add products
            if ($merchant->type == 'toko_halal') {
                MarketplaceProduct::updateOrCreate(['name' => 'Madu Arab Murni'], [
                    'merchant_id' => $merchant->id,
                    'description' => 'Madu Arab kualitas premium, asli dari Yaman.',
                    'price' => 150000,
                    'category' => 'food',
                    'is_halal_certified' => true,
                    'stock' => 50,
                ]);
                MarketplaceProduct::updateOrCreate(['name' => 'Habbatussauda Oil'], [
                    'merchant_id' => $merchant->id,
                    'description' => 'Minyak Habbatussauda murni untuk kesehatan.',
                    'price' => 85000,
                    'category' => 'medicine',
                    'is_halal_certified' => true,
                    'stock' => 100,
                ]);
            } else {
                MarketplaceProduct::updateOrCreate(['name' => 'Ayam Penyet Sambal Ijo'], [
                    'merchant_id' => $merchant->id,
                    'description' => 'Ayam penyet dengan sambal ijo khas Padang.',
                    'price' => 25000,
                    'category' => 'food',
                    'is_halal_certified' => true,
                    'stock' => 20,
                ]);
            }
        }
    }
}
