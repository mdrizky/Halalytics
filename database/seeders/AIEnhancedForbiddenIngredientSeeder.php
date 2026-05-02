<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForbiddenIngredient;

class AIEnhancedForbiddenIngredientSeeder extends Seeder
{
    public function run()
    {
        $forbiddenIngredients = [
            [
                'name' => 'Gelatin Babi',
                'code' => 'E441',
                'aliases' => ['Pig Gelatin', 'Pork Gelatin'],
                'type' => 'Pengental & Stabilizer',
                'risk_level' => 'High',
                'reason' => 'Haram',
                'description' => 'Gelatin yang berasal dari tulang, kulit, atau jaringan ikat babi. Sering digunakan sebagai pengental dalam makanan, minuman, dan obat-obatan.',
                'source' => 'Makanan olahan, suplemen, kosmetik',
                'is_active' => true,
            ],
            [
                'name' => 'Enzim Pepsin Babi',
                'code' => 'E1101i',
                'aliases' => ['Pepsin', 'Pig Pepsin'],
                'type' => 'Enzim',
                'risk_level' => 'High',
                'reason' => 'Haram',
                'description' => 'Enzim pencernaan yang diekstrak dari lambung babi. Digunakan dalam pembuatan keju dan beberapa proses makanan.',
                'source' => 'Industri makanan, farmasi',
                'is_active' => true,
            ],
            [
                'name' => 'Lard (Lemak Babi)',
                'code' => 'E1001',
                'aliases' => ['Pig Fat', 'Pork Lard'],
                'type' => 'Lemak & Minyak',
                'risk_level' => 'High',
                'reason' => 'Haram',
                'description' => 'Lemak yang diekstrak dari jaringan adiposa babi. Digunakan sebagai bahan penggorengan dan dalam pembuatan kue.',
                'source' => 'Makanan goreng, kue, pastry',
                'is_active' => true,
            ],
            [
                'name' => 'Albumin Telur',
                'code' => 'E1105',
                'aliases' => ['Egg Albumin', 'Egg White Protein'],
                'type' => 'Protein',
                'risk_level' => 'Medium',
                'reason' => 'Haram',
                'description' => 'Protein yang diekstrak dari putih telur. Digunakan sebagai pengikat dan stabilizer dalam makanan.',
                'source' => 'Produk bakery, minuman',
                'is_active' => true,
            ],
            [
                'name' => 'Kasein Asam',
                'code' => 'E1200',
                'aliases' => ['Acid Casein', 'Milk Protein'],
                'type' => 'Protein Susu',
                'risk_level' => 'Medium',
                'reason' => 'Haram',
                'description' => 'Protein susu yang diperoleh melalui proses pengasaman. Digunakan dalam industri makanan dan farmasi.',
                'source' => 'Produk susu olahan, suplemen',
                'is_active' => true,
            ],
            [
                'name' => 'Lipase Babi',
                'code' => 'E1104',
                'aliases' => ['Pig Lipase', 'Pancreatic Lipase'],
                'type' => 'Enzim',
                'risk_level' => 'High',
                'reason' => 'Haram',
                'description' => 'Enzim yang memecah lemak, diekstrak dari pankreas babi. Digunakan dalam industri makanan dan deterjen.',
                'source' => 'Industri makanan, deterjen',
                'is_active' => true,
            ],
            [
                'name' => 'Hemoglobin',
                'code' => 'E122',
                'aliases' => ['Blood Protein', 'Haemoglobin'],
                'type' => 'Pewarna',
                'risk_level' => 'Medium',
                'reason' => 'Haram',
                'description' => 'Protein pembawa oksigen dalam darah. Kadang digunakan sebagai pewarna makanan.',
                'source' => 'Produk daging olahan',
                'is_active' => true,
            ],
            [
                'name' => 'Kolesterol',
                'code' => 'E101',
                'aliases' => ['Cholesterol', 'Animal Sterol'],
                'type' => 'Sterol',
                'risk_level' => 'Medium',
                'reason' => 'Haram',
                'description' => 'Sterol yang terdapat dalam jaringan hewan. Digunakan dalam industri makanan dan farmasi.',
                'source' => 'Suplemen, vitamin',
                'is_active' => true,
            ],
            [
                'name' => 'Triptofan',
                'code' => 'E101',
                'aliases' => ['Tryptophan', 'Amino Acid'],
                'type' => 'Asam Amino',
                'risk_level' => 'Low',
                'reason' => 'Haram',
                'description' => 'Asam amino esensial yang kadang diekstrak dari bahan hewani.',
                'source' => 'Suplemen nutrisi',
                'is_active' => true,
            ],
            [
                'name' => 'Laktosa',
                'code' => 'E539',
                'aliases' => ['Lactose', 'Milk Sugar'],
                'type' => 'Gula',
                'risk_level' => 'Low',
                'reason' => 'Halal',
                'description' => 'Gula susu yang terdapat dalam susu. Bukan haram tetapi dapat bermasalah untuk intoleran laktosa.',
                'source' => 'Produk susu',
                'is_active' => true,
            ],
            [
                'name' => 'Kafein',
                'code' => 'E101',
                'aliases' => ['Caffeine', 'Stimulant'],
                'type' => 'Stimulan',
                'risk_level' => 'Low',
                'reason' => 'Halal',
                'description' => 'Stimulan alami yang terdapat dalam kopi, teh, dan kakao.',
                'source' => 'Kopi, teh, minuman energi',
                'is_active' => true,
            ],
            [
                'name' => 'Natrium Benzoat',
                'code' => 'E211',
                'aliases' => ['Sodium Benzoate', 'Preservative'],
                'type' => 'Pengawet',
                'risk_level' => 'Medium',
                'reason' => 'Halal',
                'description' => 'Pengawet sintetis yang efektif melawan bakteri dan jamur.',
                'source' => 'Makanan olahan, minuman',
                'is_active' => true,
            ],
        ];

        foreach ($forbiddenIngredients as $ingredient) {
            ForbiddenIngredient::updateOrCreate(
                ['name' => $ingredient['name']],
                $ingredient
            );
        }
    }
}