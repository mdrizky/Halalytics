<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HalalCriticalIngredient;

class HalalCriticalIngredientSeeder extends Seeder
{
    public function run()
    {
        $ingredients = [
            [
                'name' => 'Gelatin',
                'status' => 'syubhat',
                'description' => 'Protein yang diekstrak dari jaringan hewan',
                'critical_reason' => 'Berasal dari sumber hewani, bisa dari sapi (halal) atau babi (haram)',
                'common_sources' => 'Babi, Sapi, Ikan',
                'alternatives' => 'Pektin, Agar-agar, Karagenen (nabati)',
                'active' => true
            ],
            [
                'name' => 'Magnesium Stearat',
                'status' => 'syubhat',
                'description' => 'Pengemulsi yang sering digunakan dalam obat',
                'critical_reason' => 'Berasal dari lemak hewani atau nabati, sulit diketahui sumbernya',
                'common_sources' => 'Lemak Sapi, Lemak Babi, Minyak Nabati',
                'alternatives' => 'Magnesium Stearat nabati (bersertifikat halal)',
                'active' => true
            ],
            [
                'name' => 'Gliserin/Gliserol',
                'status' => 'syubhat',
                'description' => 'Pelarut yang sering digunakan dalam sirup obat',
                'critical_reason' => 'Berasal dari lemak hewan atau sintetis nabati',
                'common_sources' => 'Lemak Hewan, Minyak Nabati',
                'alternatives' => 'Gliserin nabati (bersertifikat halal)',
                'active' => true
            ],
            [
                'name' => 'Alkohol/Etanol',
                'status' => 'syubhat',
                'description' => 'Pelarut dalam obat',
                'critical_reason' => 'Kandungan dan sumber perlu diperiksa',
                'common_sources' => 'Fermentasi, Sintetis',
                'alternatives' => 'Air, Propilen Glikol',
                'active' => true
            ],
            [
                'name' => 'Laktosa',
                'status' => 'halal',
                'description' => 'Gula dari susu',
                'critical_reason' => 'Aman, tapi perlu cek proses produksi',
                'common_sources' => 'Susu Sapi, Susu Kambing',
                'alternatives' => 'Tidak perlu alternatif',
                'active' => true
            ],
            [
                'name' => 'Heparin',
                'status' => 'haram',
                'description' => 'Pengencer darah',
                'critical_reason' => 'Sering berasal dari usus babi',
                'common_sources' => 'Usus Babi, Usus Sapi',
                'alternatives' => 'Warfarin sintetis',
                'active' => true
            ],
            [
                'name' => 'Musang/Musk',
                'status' => 'syubhat',
                'description' => 'Fragrance dalam obat',
                'critical_reason' => 'Berasal dari kelenjar hewan',
                'common_sources' => 'Rusa, Musang, Sintetis',
                'alternatives' => 'Parfum sintetis',
                'active' => true
            ],
            [
                'name' => 'Enzim',
                'status' => 'syubhat',
                'description' => 'Katalisator dalam produksi obat',
                'critical_reason' => 'Berasal dari hewan, mikroba, atau nabati',
                'common_sources' => 'Pankreas Babi, Mikroba, Tumbuhan',
                'alternatives' => 'Enzim nabati atau sintetis',
                'active' => true
            ],
            [
                'name' => 'Kolesterol',
                'status' => 'syubhat',
                'description' => 'Lemak dalam kapsul obat',
                'critical_reason' => 'Berasal dari hewan atau nabati',
                'common_sources' => 'Lemak Sapi, Lemak Babi, Minyak Nabati',
                'alternatives' => 'Kolesterol nabati',
                'active' => true
            ],
            [
                'name' => 'Kasein',
                'status' => 'halal',
                'description' => 'Protein susu',
                'critical_reason' => 'Aman, tapi perlu cek enzim yang digunakan',
                'common_sources' => 'Susu Sapi',
                'alternatives' => 'Tidak perlu alternatif',
                'active' => true
            ]
        ];

        foreach ($ingredients as $ingredient) {
            HalalCriticalIngredient::firstOrCreate(
                ['name' => $ingredient['name']],
                $ingredient
            );
        }
    }
}
