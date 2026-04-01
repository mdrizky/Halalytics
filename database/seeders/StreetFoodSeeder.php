<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StreetFood;
use App\Models\FoodVariant;

class StreetFoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder untuk makanan sajian Indonesia populer
     */
    public function run(): void
    {
        // Avoid duplicate demo data when seeder is executed multiple times.
        if (StreetFood::query()->exists()) {
            $this->command?->info('Street foods already seeded, skipping.');
            return;
        }

        // ========== NASI GORENG ==========
        $nasiGoreng = StreetFood::create([
            'name' => 'Nasi Goreng',
            'name_en' => 'Fried Rice',
            'slug' => 'nasi-goreng',
            'description' => 'Nasi yang digoreng dengan bumbu kecap, bawang, dan berbagai topping',
            'category' => 'Nasi',
            'calories_min' => 280,
            'calories_max' => 450,
            'calories_typical' => 320,
            'protein' => 8.5,
            'carbs' => 45,
            'fat' => 12,
            'fiber' => 2.5,
            'sugar' => 4,
            'sodium' => 800,
            'serving_size_grams' => 250,
            'serving_description' => '1 piring',
            'halal_status' => 'halal_umum',
            'halal_notes' => 'Umumnya halal, tapi perhatikan kecap dan minyak yang digunakan.',
            'health_tags' => ['tinggi_karbohidrat', 'tinggi_natrium'],
            'health_notes' => 'Mengandung karbohidrat tinggi dan minyak.',
            'ai_keywords' => ['nasi', 'goreng', 'rice', 'fried', 'nasgor', 'nasi goreng'],
            'common_ingredients' => ['nasi', 'kecap', 'bawang merah', 'bawang putih', 'telur', 'minyak goreng'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $nasiGoreng->id, 'variant_name' => 'Nasi Goreng Biasa', 'variant_type' => 'basic', 'is_default' => true, 'popularity' => 100]);
        FoodVariant::create(['street_food_id' => $nasiGoreng->id, 'variant_name' => 'Nasi Goreng Telur', 'variant_type' => 'topping', 'calories_modifier' => 70, 'protein_modifier' => 6, 'fat_modifier' => 5, 'popularity' => 95]);
        FoodVariant::create(['street_food_id' => $nasiGoreng->id, 'variant_name' => 'Nasi Goreng Ayam', 'variant_type' => 'topping', 'calories_modifier' => 90, 'protein_modifier' => 12, 'fat_modifier' => 4, 'popularity' => 90]);
        FoodVariant::create(['street_food_id' => $nasiGoreng->id, 'variant_name' => 'Nasi Goreng Seafood', 'variant_type' => 'topping', 'calories_modifier' => 100, 'protein_modifier' => 15, 'fat_modifier' => 5, 'popularity' => 80]);

        // ========== MIE GORENG ==========
        $mieGoreng = StreetFood::create([
            'name' => 'Mie Goreng',
            'name_en' => 'Fried Noodles',
            'slug' => 'mie-goreng',
            'description' => 'Mie yang digoreng dengan bumbu kecap dan sayuran',
            'category' => 'Mie',
            'calories_min' => 300,
            'calories_max' => 500,
            'calories_typical' => 380,
            'protein' => 10,
            'carbs' => 52,
            'fat' => 14,
            'fiber' => 3,
            'sugar' => 5,
            'sodium' => 950,
            'serving_size_grams' => 280,
            'serving_description' => '1 piring',
            'halal_status' => 'halal_umum',
            'halal_notes' => 'Perhatikan jenis mie instant jika digunakan.',
            'health_tags' => ['tinggi_karbohidrat', 'tinggi_natrium'],
            'ai_keywords' => ['mie', 'mi', 'goreng', 'noodle', 'fried noodle', 'mie goreng'],
            'common_ingredients' => ['mie', 'kecap', 'sayuran', 'bawang', 'cabai'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $mieGoreng->id, 'variant_name' => 'Mie Goreng Biasa', 'variant_type' => 'basic', 'is_default' => true, 'popularity' => 100]);
        FoodVariant::create(['street_food_id' => $mieGoreng->id, 'variant_name' => 'Mie Goreng Telur', 'variant_type' => 'topping', 'calories_modifier' => 70, 'protein_modifier' => 6, 'popularity' => 90]);
        FoodVariant::create(['street_food_id' => $mieGoreng->id, 'variant_name' => 'Mie Goreng Ayam', 'variant_type' => 'topping', 'calories_modifier' => 90, 'protein_modifier' => 12, 'popularity' => 85]);

        // ========== MIE REBUS ==========
        $mieRebus = StreetFood::create([
            'name' => 'Mie Rebus',
            'name_en' => 'Boiled Noodles',
            'slug' => 'mie-rebus',
            'description' => 'Mie dalam kuah kaldu yang hangat',
            'category' => 'Mie',
            'calories_min' => 250,
            'calories_max' => 420,
            'calories_typical' => 300,
            'protein' => 9,
            'carbs' => 48,
            'fat' => 8,
            'fiber' => 2.5,
            'sugar' => 3,
            'sodium' => 1100,
            'serving_size_grams' => 320,
            'serving_description' => '1 mangkok',
            'halal_status' => 'halal_umum',
            'halal_notes' => 'Perhatikan kaldu yang digunakan.',
            'health_tags' => ['tinggi_natrium', 'berkuah'],
            'ai_keywords' => ['mie', 'rebus', 'kuah', 'boiled noodle', 'soup noodle'],
            'common_ingredients' => ['mie', 'kaldu', 'sayuran', 'bawang'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $mieRebus->id, 'variant_name' => 'Mie Rebus Biasa', 'variant_type' => 'basic', 'is_default' => true, 'popularity' => 100]);
        FoodVariant::create(['street_food_id' => $mieRebus->id, 'variant_name' => 'Mie Ayam', 'variant_type' => 'topping', 'calories_modifier' => 80, 'protein_modifier' => 10, 'popularity' => 95]);
        FoodVariant::create(['street_food_id' => $mieRebus->id, 'variant_name' => 'Mie Bakso', 'variant_type' => 'topping', 'calories_modifier' => 100, 'protein_modifier' => 8, 'popularity' => 90]);

        // ========== MARTABAK MANIS ==========
        $martabakManis = StreetFood::create([
            'name' => 'Martabak Manis',
            'name_en' => 'Sweet Martabak',
            'slug' => 'martabak-manis',
            'description' => 'Kue tebal dengan topping manis seperti coklat, keju, kacang',
            'category' => 'Kue',
            'calories_min' => 450,
            'calories_max' => 700,
            'calories_typical' => 550,
            'protein' => 8,
            'carbs' => 65,
            'fat' => 25,
            'fiber' => 2,
            'sugar' => 30,
            'sodium' => 350,
            'serving_size_grams' => 200,
            'serving_description' => '1 potong (1/4 loyang)',
            'halal_status' => 'halal_umum',
            'halal_notes' => 'Perhatikan topping seperti coklat dan keju.',
            'health_tags' => ['tinggi_kalori', 'tinggi_gula', 'tinggi_lemak'],
            'health_notes' => 'Sangat tinggi kalori, gula, dan lemak. Konsumsi sesekali saja.',
            'ai_keywords' => ['martabak', 'terang bulan', 'sweet pancake', 'martabak manis'],
            'common_ingredients' => ['tepung', 'telur', 'gula', 'mentega', 'coklat', 'keju', 'kacang'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $martabakManis->id, 'variant_name' => 'Martabak Coklat', 'variant_type' => 'topping', 'is_default' => true, 'popularity' => 100]);
        FoodVariant::create(['street_food_id' => $martabakManis->id, 'variant_name' => 'Martabak Keju', 'variant_type' => 'topping', 'calories_modifier' => 50, 'fat_modifier' => 5, 'popularity' => 95]);
        FoodVariant::create(['street_food_id' => $martabakManis->id, 'variant_name' => 'Martabak Coklat Keju', 'variant_type' => 'topping', 'calories_modifier' => 80, 'fat_modifier' => 7, 'popularity' => 90]);

        // ========== MARTABAK TELUR ==========
        $martabakTelur = StreetFood::create([
            'name' => 'Martabak Telur',
            'name_en' => 'Egg Martabak',
            'slug' => 'martabak-telur',
            'description' => 'Martabak asin berisi telur, daging, dan sayuran',
            'category' => 'Gorengan',
            'calories_min' => 320,
            'calories_max' => 480,
            'calories_typical' => 380,
            'protein' => 15,
            'carbs' => 35,
            'fat' => 18,
            'fiber' => 2,
            'sugar' => 2,
            'sodium' => 650,
            'serving_size_grams' => 180,
            'serving_description' => '1 porsi',
            'halal_status' => 'tergantung_bahan',
            'halal_notes' => 'PERHATIAN: Isian daging harus halal. Tanyakan ke penjual.',
            'health_tags' => ['tinggi_protein', 'tinggi_lemak'],
            'ai_keywords' => ['martabak', 'telur', 'egg martabak', 'martabak asin'],
            'common_ingredients' => ['tepung', 'telur', 'daging', 'daun bawang', 'bawang bombay'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $martabakTelur->id, 'variant_name' => 'Martabak Telur Biasa', 'variant_type' => 'basic', 'is_default' => true, 'popularity' => 100]);
        FoodVariant::create(['street_food_id' => $martabakTelur->id, 'variant_name' => 'Martabak Telur Daging', 'variant_type' => 'topping', 'calories_modifier' => 60, 'protein_modifier' => 8, 'popularity' => 90]);

        // ========== LONTONG SAYUR ==========
        $lontongSayur = StreetFood::create([
            'name' => 'Lontong Sayur',
            'name_en' => 'Rice Cake with Vegetable Curry',
            'slug' => 'lontong-sayur',
            'description' => 'Lontong dengan kuah santan sayur dan sambal',
            'category' => 'Berkuah',
            'calories_min' => 280,
            'calories_max' => 420,
            'calories_typical' => 340,
            'protein' => 7,
            'carbs' => 50,
            'fat' => 12,
            'fiber' => 5,
            'sugar' => 4,
            'sodium' => 700,
            'serving_size_grams' => 300,
            'serving_description' => '1 porsi',
            'halal_status' => 'halal_umum',
            'halal_notes' => 'Umumnya halal.',
            'health_tags' => ['tinggi_karbohidrat', 'tinggi_serat'],
            'ai_keywords' => ['lontong', 'sayur', 'rice cake', 'vegetable curry'],
            'common_ingredients' => ['lontong', 'santan', 'sayuran', 'sambal'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $lontongSayur->id, 'variant_name' => 'Lontong Sayur Biasa', 'variant_type' => 'basic', 'is_default' => true, 'popularity' => 100]);
        FoodVariant::create(['street_food_id' => $lontongSayur->id, 'variant_name' => 'Lontong Sayur Telur', 'variant_type' => 'topping', 'calories_modifier' => 70, 'protein_modifier' => 6, 'popularity' => 85]);

        // ========== PECEL ==========
        $pecel = StreetFood::create([
            'name' => 'Pecel',
            'name_en' => 'Javanese Vegetable Salad',
            'slug' => 'pecel',
            'description' => 'Sayuran rebus dengan saus kacang pedas',
            'category' => 'Sayuran',
            'calories_min' => 180,
            'calories_max' => 320,
            'calories_typical' => 220,
            'protein' => 8,
            'carbs' => 25,
            'fat' => 10,
            'fiber' => 7,
            'sugar' => 5,
            'sodium' => 450,
            'serving_size_grams' => 220,
            'serving_description' => '1 porsi',
            'halal_status' => 'halal_umum',
            'halal_notes' => 'Halal. Terdiri dari sayuran dan bumbu kacang.',
            'health_tags' => ['tinggi_serat', 'rendah_kalori', 'sehat'],
            'health_notes' => 'Sangat sehat, tinggi serat dan vitamin.',
            'ai_keywords' => ['pecel', 'vegetable', 'salad', 'kacang', 'peanut sauce'],
            'common_ingredients' => ['kangkung', 'bayam', 'kacang panjang', 'tauge', 'bumbu kacang'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $pecel->id, 'variant_name' => 'Pecel Biasa', 'variant_type' => 'basic', 'is_default' => true, 'popularity' => 100]);
        FoodVariant::create(['street_food_id' => $pecel->id, 'variant_name' => 'Pecel + Nasi', 'variant_type' => 'basic', 'calories_modifier' => 150, 'carbs_modifier' => 35, 'popularity' => 90]);

        // ========== SOTO AYAM ==========
        $sotoAyam = StreetFood::create([
            'name' => 'Soto Ayam',
            'name_en' => 'Chicken Soup',
            'slug' => 'soto-ayam',
            'description' => 'Sup ayam khas Indonesia dengan bumbu kunyit',
            'category' => 'Berkuah',
            'calories_min' => 200,
            'calories_max' => 380,
            'calories_typical' => 280,
            'protein' => 18,
            'carbs' => 25,
            'fat' => 12,
            'fiber' => 2,
            'sugar' => 2,
            'sodium' => 900,
            'serving_size_grams' => 350,
            'serving_description' => '1 mangkok',
            'halal_status' => 'halal_umum',
            'halal_notes' => 'Umumnya halal. Pastikan ayam halal.',
            'health_tags' => ['tinggi_protein'],
            'ai_keywords' => ['soto', 'ayam', 'chicken soup', 'soto ayam'],
            'common_ingredients' => ['ayam', 'kunyit', 'soun', 'telur', 'kentang', 'bawang goreng'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $sotoAyam->id, 'variant_name' => 'Soto Ayam Biasa', 'variant_type' => 'basic', 'is_default' => true, 'popularity' => 100]);
        FoodVariant::create(['street_food_id' => $sotoAyam->id, 'variant_name' => 'Soto Ayam + Nasi', 'variant_type' => 'basic', 'calories_modifier' => 150, 'carbs_modifier' => 35, 'popularity' => 95]);

        // ========== BAKSO ==========
        $bakso = StreetFood::create([
            'name' => 'Bakso',
            'name_en' => 'Meatball Soup',
            'slug' => 'bakso',
            'description' => 'Bola daging sapi dalam kuah kaldu',
            'category' => 'Berkuah',
            'calories_min' => 250,
            'calories_max' => 400,
            'calories_typical' => 320,
            'protein' => 16,
            'carbs' => 30,
            'fat' => 14,
            'fiber' => 2,
            'sugar' => 2,
            'sodium' => 1000,
            'serving_size_grams' => 350,
            'serving_description' => '1 mangkok',
            'halal_status' => 'tergantung_bahan',
            'halal_notes' => 'PERHATIAN: Pastikan bakso tidak mengandung babi. Tanya penjual.',
            'health_tags' => ['tinggi_protein', 'tinggi_natrium'],
            'ai_keywords' => ['bakso', 'baso', 'meatball', 'meatball soup'],
            'common_ingredients' => ['bakso sapi', 'mie', 'tahu', 'kaldu', 'bawang goreng'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $bakso->id, 'variant_name' => 'Bakso Biasa', 'variant_type' => 'basic', 'is_default' => true, 'popularity' => 100]);
        FoodVariant::create(['street_food_id' => $bakso->id, 'variant_name' => 'Bakso Urat', 'variant_type' => 'topping', 'calories_modifier' => 30, 'protein_modifier' => 5, 'popularity' => 90]);
        FoodVariant::create(['street_food_id' => $bakso->id, 'variant_name' => 'Bakso Jumbo', 'variant_type' => 'size', 'calories_modifier' => 80, 'protein_modifier' => 10, 'popularity' => 85]);

        // ========== GADO-GADO ==========
        $gadoGado = StreetFood::create([
            'name' => 'Gado-Gado',
            'name_en' => 'Indonesian Salad',
            'slug' => 'gado-gado',
            'description' => 'Sayuran matang dengan saus kacang dan kerupuk',
            'category' => 'Sayuran',
            'calories_min' => 250,
            'calories_max' => 400,
            'calories_typical' => 300,
            'protein' => 12,
            'carbs' => 30,
            'fat' => 15,
            'fiber' => 8,
            'sugar' => 6,
            'sodium' => 500,
            'serving_size_grams' => 280,
            'serving_description' => '1 porsi',
            'halal_status' => 'halal_umum',
            'halal_notes' => 'Halal. Terdiri dari sayuran, tahu, tempe, dan bumbu kacang.',
            'health_tags' => ['tinggi_serat', 'tinggi_protein'],
            'health_notes' => 'Sehat, kaya serat dan protein nabati.',
            'ai_keywords' => ['gado', 'gado-gado', 'indonesian salad', 'peanut salad'],
            'common_ingredients' => ['sayuran', 'tahu', 'tempe', 'telur', 'kentang', 'bumbu kacang', 'kerupuk'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $gadoGado->id, 'variant_name' => 'Gado-Gado Biasa', 'variant_type' => 'basic', 'is_default' => true, 'popularity' => 100]);
        FoodVariant::create(['street_food_id' => $gadoGado->id, 'variant_name' => 'Gado-Gado + Lontong', 'variant_type' => 'basic', 'calories_modifier' => 120, 'carbs_modifier' => 28, 'popularity' => 90]);

        // ========== NASI UDUK ==========
        $nasiUduk = StreetFood::create([
            'name' => 'Nasi Uduk',
            'name_en' => 'Coconut Rice',
            'slug' => 'nasi-uduk',
            'description' => 'Nasi yang dimasak dengan santan dan rempah',
            'category' => 'Nasi',
            'calories_min' => 280,
            'calories_max' => 450,
            'calories_typical' => 350,
            'protein' => 6,
            'carbs' => 55,
            'fat' => 12,
            'fiber' => 2,
            'sugar' => 2,
            'sodium' => 400,
            'serving_size_grams' => 200,
            'serving_description' => '1 bungkus',
            'halal_status' => 'halal_umum',
            'halal_notes' => 'Halal. Perhatikan lauk yang menyertai.',
            'health_tags' => ['tinggi_karbohidrat'],
            'ai_keywords' => ['nasi', 'uduk', 'coconut rice', 'nasi uduk'],
            'common_ingredients' => ['nasi', 'santan', 'sereh', 'daun salam'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $nasiUduk->id, 'variant_name' => 'Nasi Uduk Biasa', 'variant_type' => 'basic', 'is_default' => true, 'popularity' => 100]);
        FoodVariant::create(['street_food_id' => $nasiUduk->id, 'variant_name' => 'Nasi Uduk Ayam Goreng', 'variant_type' => 'topping', 'calories_modifier' => 150, 'protein_modifier' => 20, 'fat_modifier' => 10, 'popularity' => 95]);

        // ========== KETOPRAK ==========
        $ketoprak = StreetFood::create([
            'name' => 'Ketoprak',
            'name_en' => 'Ketoprak',
            'slug' => 'ketoprak',
            'description' => 'Lontong, tahu, bihun dengan saus kacang',
            'category' => 'Sayuran',
            'calories_min' => 280,
            'calories_max' => 400,
            'calories_typical' => 330,
            'protein' => 10,
            'carbs' => 45,
            'fat' => 12,
            'fiber' => 4,
            'sugar' => 6,
            'sodium' => 550,
            'serving_size_grams' => 250,
            'serving_description' => '1 porsi',
            'halal_status' => 'halal_umum',
            'halal_notes' => 'Halal.',
            'health_tags' => ['tinggi_karbohidrat'],
            'ai_keywords' => ['ketoprak', 'lontong', 'tahu', 'peanut sauce'],
            'common_ingredients' => ['lontong', 'tahu', 'bihun', 'tauge', 'bumbu kacang', 'kerupuk'],
            'is_popular' => true,
            'is_active' => true
        ]);

        FoodVariant::create(['street_food_id' => $ketoprak->id, 'variant_name' => 'Ketoprak Biasa', 'variant_type' => 'basic', 'is_default' => true, 'popularity' => 100]);
    }
}
