<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = [
            [
                'name' => 'Bodrex Extra',
                'generic_name' => 'Paracetamol, Caffeine',
                'brand_name' => 'Bodrex',
                'barcode' => '8999908000101',
                'image_url' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/7/3/2a5ebbf1-3a4f-429e-b3b4-40fb3e0cf7ec.jpg',
                'description' => 'Pereda sakit kepala dengan kombinasi analgesik dan kafein.',
                'indications' => 'Sakit kepala, nyeri ringan sampai sedang.',
                'ingredients' => ['Paracetamol 500mg', 'Caffeine 50mg'],
                'dosage_info' => 'Dewasa: 1 tablet, dapat diulang tiap 4-6 jam jika perlu.',
                'side_effects' => 'Mual ringan, sulit tidur jika dikonsumsi malam hari.',
                'halal_status' => 'halal',
                'manufacturer' => 'Tempo Scan Pacific',
                'dosage_form' => 'Tablet',
                'category' => 'Analgesik',
                'active' => true
            ],
            [
                'name' => 'Bodrex Migra',
                'generic_name' => 'Paracetamol, Propyphenazone, Caffeine',
                'brand_name' => 'Bodrex',
                'barcode' => '8999908000102',
                'image_url' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/2/20/234403d8-fbe4-418c-a5e0-66d3de7f6cd4.jpg',
                'description' => 'Obat sakit kepala tegang dengan kombinasi analgesik.',
                'indications' => 'Meredakan sakit kepala sebelah atau tegang.',
                'ingredients' => ['Paracetamol 350mg', 'Propyphenazone 150mg', 'Caffeine 50mg'],
                'dosage_info' => 'Dewasa: 1 tablet, maksimal 3 kali sehari setelah makan.',
                'side_effects' => 'Mual, berdebar, gangguan lambung.',
                'halal_status' => 'halal',
                'manufacturer' => 'Tempo Scan Pacific',
                'dosage_form' => 'Tablet',
                'category' => 'Analgesik',
                'active' => true
            ],
            [
                'name' => 'Paracetamol 500mg',
                'generic_name' => 'Paracetamol',
                'brand_name' => 'Generic',
                'barcode' => '8991234567001',
                'image_url' => 'https://static2.helosehat.com/wp-content/uploads/2016/11/paracetamol.jpg',
                'description' => 'Obat pereda nyeri dan penurun demam.',
                'indications' => 'Meredakan sakit kepala, sakit gigi, dan menurunkan demam.',
                'ingredients' => ['Paracetamol 500mg'],
                'dosage_info' => 'Dewasa: 1-2 tablet, 3-4 kali sehari.',
                'side_effects' => 'Penggunaan jangka panjang dapat menyebabkan kerusakan hati.',
                'halal_status' => 'halal',
                'manufacturer' => 'Kimia Farma',
                'dosage_form' => 'Tablet',
                'category' => 'Analgesik',
                'active' => true
            ],
            [
                'name' => 'Amoxicillin 500mg',
                'generic_name' => 'Amoxicillin Trihydrate',
                'brand_name' => 'Generic',
                'barcode' => '8991234567002',
                'image_url' => 'https://d2qjkwm11akmwu.cloudfront.net/products/126986_19-3-2019_10-33-3.jpg',
                'description' => 'Antibiotik golongan penisilin.',
                'indications' => 'Infeksi saluran pernapasan, infeksi saluran kemih.',
                'ingredients' => ['Amoxicillin 500mg'],
                'dosage_info' => 'Dewasa: 1 tablet setiap 8 jam.',
                'side_effects' => 'Mual, diare, ruam kulit.',
                'halal_status' => 'halal',
                'manufacturer' => 'Indofarma',
                'dosage_form' => 'Kaplet',
                'category' => 'Antibiotik',
                'is_prescription_required' => true,
                'active' => true
            ],
            [
                'name' => 'Promag Tablet',
                'generic_name' => 'Hydrotalcite, Magnesium Hydroxide, Simethicone',
                'brand_name' => 'Promag',
                'barcode' => '8991234567003',
                'image_url' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/4/22/0c79f97d-6c17-48f8-b364-7936a7edc8e6.jpg',
                'description' => 'Obat sakit maag dan kembung.',
                'indications' => 'Meringankan gejala sakit maag karena asam lambung.',
                'ingredients' => ['Hydrotalcite 200mg', 'Magnesium Hydroxide 150mg', 'Simethicone 50mg'],
                'dosage_info' => 'Dewasa: 1-2 tablet, 3-4 kali sehari.',
                'side_effects' => 'Sembelit, diare.',
                'halal_status' => 'halal',
                'manufacturer' => 'Kalbe Farma',
                'dosage_form' => 'Tablet Kunyah',
                'category' => 'Antasida',
                'active' => true
            ],
            [
                'name' => 'OBH Combi Batuk & Flu',
                'generic_name' => 'Succus Liquiritiae, Paracetamol, Ammonium Chloride, Ephedrine HCl, Chlorpheniramine Maleate',
                'brand_name' => 'OBH Combi',
                'barcode' => '8991234567004',
                'image_url' => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//94/MTA-3801452/obh-combi_obh-combi-batuk---flu-menthol-obat-kesehatan--100-ml-_full02.jpg',
                'description' => 'Obat batuk dan flu.',
                'indications' => 'Meredakan batuk yang disertai gejala flu seperti demam dan sakit kepala.',
                'ingredients' => ['Succus Liquiritiae', 'Paracetamol', 'Ammonium Chloride'],
                'dosage_info' => 'Dewasa: 3 x sehari 15 ml.',
                'side_effects' => 'Mengantuk.',
                'halal_status' => 'halal',
                'manufacturer' => 'Combiphar',
                'dosage_form' => 'Sirup',
                'category' => 'Obat Batuk',
                'active' => true
            ],
            [
                'name' => 'Panadol Menstruasi',
                'generic_name' => 'Paracetamol, Caffeine',
                'brand_name' => 'Panadol',
                'barcode' => '8991234567005',
                'image_url' => 'https://d2qjkwm11akmwu.cloudfront.net/products/430815_12-7-2022_10-53-4.jpg',
                'description' => 'Meredakan nyeri saat menstruasi.',
                'indications' => 'Sakit perut dan nyeri badan saat haid.',
                'ingredients' => ['Paracetamol 500mg', 'Caffeine 65mg'],
                'dosage_info' => '1-2 tablet, tiap 4-6 jam.',
                'side_effects' => 'Insomnia, gelisah.',
                'halal_status' => 'halal',
                'manufacturer' => 'GlaxoSmithKline',
                'dosage_form' => 'Kaplet',
                'category' => 'Analgesik',
                'active' => true
            ]
        ];

        foreach ($medicines as $med) {
            Medicine::updateOrCreate(
                ['name' => $med['name']],
                $med
            );
        }
    }
}
