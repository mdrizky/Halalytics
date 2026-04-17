<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;
use App\Models\ProductModel;
use App\Models\KategoriModel;

class ProductEnrichmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data Obat (Medicine) Enrichment
        $medicines = [
            'Bodrex Extra' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/7/3/2a5ebbf1-3a4f-429e-b3b4-40fb3e0cf7ec.jpg',
            'Bodrex Migra' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/2/20/234403d8-fbe4-418c-a5e0-66d3de7f6cd4.jpg',
            'Amoxicillin 250mg' => 'https://d2qjkwm11akmwu.cloudfront.net/products/126986_19-3-2019_10-33-3.jpg',
            'Amoxicillin 500mg' => 'https://d2qjkwm11akmwu.cloudfront.net/products/126986_19-3-2019_10-33-3.jpg',
            'Ibuprofen 200mg' => 'https://upload.wikimedia.org/wikipedia/commons/5/5f/Ibuprofen_200mg_tablets.jpg',
            'Paracetamol 500mg' => 'https://static2.helosehat.com/wp-content/uploads/2016/11/paracetamol.jpg',
            'Cetirizine 10mg' => 'https://upload.wikimedia.org/wikipedia/commons/6/6a/Cetirizine_tablets.jpg',
            'Omeprazole 20mg' => 'https://upload.wikimedia.org/wikipedia/commons/3/3f/Omeprazole_capsules.jpg',
            'Metformin 500mg' => 'https://upload.wikimedia.org/wikipedia/commons/1/1c/Metformin_500mg_tablets.jpg',
            'OBH Combi Batuk & Flu' => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//94/MTA-3801452/obh-combi_obh-combi-batuk---flu-menthol-obat-kesehatan--100-ml-_full02.jpg',
            'Panadol Menstruasi' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2020/10/29/7a94e54f-3133-4fd1-8338-2d2e10f5e8dc.jpg',
        ];

        foreach ($medicines as $name => $url) {
            Medicine::where('name', 'like', "%{$name}%")->update(['image_url' => $url]);
        }

        // 2. Data Kosmetik Enrichment
        $cosmeticsCat = KategoriModel::where('nama_kategori', 'Kosmetik')->first();
        $minumanCat = KategoriModel::where('nama_kategori', 'Minuman')->first();

        // Fix Ultra Milk miscategorized as Cosmetic
        ProductModel::where('nama_product', 'like', '%Ultra Milk%')
            ->update(['kategori_id' => $minumanCat->id_kategori ?? null]);

        $cosmeticData = [
            [
                'nama_product' => 'Cetaphil Gentle Skin Cleanser 125ml',
                'barcode' => 'NA1820170020', // Using BPOM as pseudo-barcode if missing
                'image' => 'https://images.openbeautyfacts.org/images/products/349/932/000/7389/front_en.5.400.jpg',
                'status' => 'syubhat',
                'kategori_id' => $cosmeticsCat->id_kategori ?? null,
                'source' => 'OBF'
            ],
            [
                'nama_product' => 'PIXY White Aqua Gel Cream Night Cream',
                'barcode' => 'NA1820170022',
                'image' => 'https://images.openbeautyfacts.org/images/products/899/990/903/0192/front_id.4.400.jpg',
                'status' => 'halal',
                'kategori_id' => $cosmeticsCat->id_kategori ?? null,
                'source' => 'Sistem'
            ],
            [
                'nama_product' => 'Wardah UV Shield Essential Sunscreen',
                'barcode' => 'NA1820170012',
                'image' => 'https://images.openbeautyfacts.org/images/products/899/313/769/1515/front_id.5.400.jpg',
                'status' => 'halal',
                'kategori_id' => $cosmeticsCat->id_kategori ?? null,
                'source' => 'Sistem'
            ],
            [
                'nama_product' => 'Safi White Natural Brightening Cream',
                'barcode' => 'NA1820170024',
                'image' => 'https://images.openbeautyfacts.org/images/products/955/600/125/4379/front_en.3.400.jpg',
                'status' => 'halal',
                'kategori_id' => $cosmeticsCat->id_kategori ?? null,
                'source' => 'Sistem'
            ],
            [
                'nama_product' => 'The Ordinary Niacinamide 10% + Zinc',
                'barcode' => 'NA1820170026',
                'image' => 'https://images.openbeautyfacts.org/images/products/076/692/215/7323/front_en.6.400.jpg',
                'status' => 'syubhat',
                'kategori_id' => $cosmeticsCat->id_kategori ?? null,
                'source' => 'OBF'
            ],
            [
                'nama_product' => 'Vaseline Healthy White UV Lotion',
                'barcode' => 'NA1820170028',
                'image' => 'https://images.openbeautyfacts.org/images/products/899/999/905/1948/front_id.4.400.jpg',
                'status' => 'halal',
                'kategori_id' => $cosmeticsCat->id_kategori ?? null,
                'source' => 'Sistem'
            ],
            [
                'nama_product' => 'Somethinc Niacinamide Moisture Sabi',
                'barcode' => 'NA1820170016',
                'image' => 'https://images.openbeautyfacts.org/images/products/899/313/769/4103/front_id.4.400.jpg',
                'status' => 'halal',
                'kategori_id' => $cosmeticsCat->id_kategori ?? null,
                'source' => 'OBF'
            ],
            [
                'nama_product' => 'Skintific 5X Ceramide Barrier Moisture',
                'barcode' => 'NA1820170018',
                'image' => 'https://images.openbeautyfacts.org/images/products/899/999/905/2433/front_id.4.400.jpg',
                'status' => 'halal',
                'kategori_id' => $cosmeticsCat->id_kategori ?? null,
                'source' => 'OBF'
            ],
        ];

        foreach ($cosmeticData as $data) {
            ProductModel::updateOrCreate(['barcode' => $data['barcode']], $data);
        }

        // 3. Local Verified/Food Enrichment
        $foodProducts = [
            'Kecap ABC Manis' => 'https://images.openfoodfacts.org/images/products/899/100/400/0004/front_id.3.400.jpg',
            'Tango Wafer Coklat' => 'https://images.openfoodfacts.org/images/products/899/600/130/0077/front_id.6.400.jpg',
            'Pocari Sweat 500ml' => 'https://images.openfoodfacts.org/images/products/049/870/351/3141/front_en.85.400.jpg',
            'SilverQueen Cashew' => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//96/MTA-2775604/silverqueen_silverqueen-cashew-65-gr_full02.jpg',
            'Sari Gandum Sandwich' => 'https://images.openfoodfacts.org/images/products/899/600/130/0077/front_id.6.400.jpg',
            'Ultra Milk 250ml Coklat' => 'https://assets.klikindomaret.com/products/20002621/20002621_1.jpg',
        ];

        foreach ($foodProducts as $name => $url) {
            ProductModel::where('nama_product', 'like', "%{$name}%")->update(['image' => $url]);
        }
    }
}
