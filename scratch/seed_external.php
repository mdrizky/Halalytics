<?php

use App\Models\ProductModel;
use App\Models\Medicine;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\DB;

// 1. Open Food Facts (OFF)
$offProducts = [
    [
        'nama_product' => 'Oreo Chocolate Sandwich',
        'barcode' => '7622300314773',
        'image' => 'https://images.openfoodfacts.org/images/products/762/230/031/4773/front_en.115.400.jpg',
        'kategori_id' => 5, // Makanan Ringan
        'source' => 'open_food_facts',
        'status' => 'halal',
        'verification_status' => 'verified'
    ],
    [
        'nama_product' => 'Pringles Original',
        'barcode' => '5053990101572',
        'image' => 'https://images.openfoodfacts.org/images/products/505/399/010/1572/front_en.87.400.jpg',
        'kategori_id' => 5,
        'source' => 'open_food_facts',
        'status' => 'halal',
        'verification_status' => 'verified'
    ],
    [
        'nama_product' => 'Lays Classic',
        'barcode' => '028400199148',
        'image' => 'https://images.openfoodfacts.org/images/products/028/400/199/148/front_en.16.400.jpg',
        'kategori_id' => 5,
        'source' => 'open_food_facts',
        'status' => 'halal',
        'verification_status' => 'verified'
    ]
];

foreach ($offProducts as $p) {
    ProductModel::updateOrCreate(['barcode' => $p['barcode']], $p);
}

// 2. Open Beauty Facts (OBF)
$obfProducts = [
    [
        'nama_product' => 'The Ordinary Niacinamide 10%',
        'barcode' => '7622123456789',
        'image' => 'https://images.openbeautyfacts.org/images/products/762/212/345/6789/front_en.1.400.jpg',
        'kategori_id' => 4, // Skincare
        'source' => 'open_beauty_facts',
        'status' => 'halal',
        'verification_status' => 'verified'
    ],
    [
        'nama_product' => 'CeraVe Hydrating Cleanser',
        'barcode' => '3337875597198',
        'image' => 'https://images.openbeautyfacts.org/images/products/333/787/559/7198/front_en.14.400.jpg',
        'kategori_id' => 4,
        'source' => 'open_beauty_facts',
        'status' => 'halal',
        'verification_status' => 'verified'
    ],
    [
        'nama_product' => 'Laneige Water Bank Cream',
        'barcode' => '8809643063517',
        'image' => 'https://images.openbeautyfacts.org/images/products/880/964/306/3517/front_en.3.400.jpg',
        'kategori_id' => 4,
        'source' => 'open_beauty_facts',
        'status' => 'halal',
        'verification_status' => 'verified'
    ]
];

foreach ($obfProducts as $p) {
    ProductModel::updateOrCreate(['barcode' => $p['barcode']], $p);
}

// 3. OpenFDA (Medicines)
$fdaMedicines = [
    [
        'name' => 'Advil Liqui-Gels',
        'generic_name' => 'Ibuprofen',
        'brand_name' => 'Advil',
        'barcode' => '305730169408',
        'image' => 'https://www.advil.com/content/dam/cf-consumer-healthcare/bp-advil/en_US/products/advil-liqui-gels/advil_liquigels_20ct_v2.png',
        'source' => 'openfda',
        'halal_status' => 'halal',
        'verification_status' => 'verified'
    ],
    [
        'name' => 'Tylenol Extra Strength',
        'generic_name' => 'Acetaminophen',
        'brand_name' => 'Tylenol',
        'barcode' => '300450449103',
        'image' => 'https://www.tylenol.com/sites/tylenol_us/files/products/tyl_extrastrength_50ct_0.png',
        'source' => 'openfda',
        'halal_status' => 'halal',
        'verification_status' => 'verified'
    ],
    [
        'name' => 'Centrum Silver Adults',
        'generic_name' => 'Multivitamin',
        'brand_name' => 'Centrum',
        'barcode' => '300054473652',
        'image' => 'https://www.centrum.com/content/dam/cf-consumer-healthcare/bp-centrum/en_US/products/centrum-silver-adults/Centrum-Silver-Adults-80ct.png',
        'source' => 'openfda',
        'halal_status' => 'halal',
        'verification_status' => 'verified'
    ]
];

foreach ($fdaMedicines as $m) {
    Medicine::updateOrCreate(['barcode' => $m['barcode']], $m);
}

echo "Seeding completed successfully!";
