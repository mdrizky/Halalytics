<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StreetFood;
use App\Models\FoodVariant;
use App\Services\AIProductAnalysisService;

class StreetFoodSeeder extends Seeder
{
    protected AIProductAnalysisService $aiService;

    public function __construct()
    {
        $this->aiService = new AIProductAnalysisService();
    }

    /**
     * Run the database seeds.
     *
     * Seeder untuk makanan sajian Indonesia populer dengan analisis AI
     */
    public function run(): void
    {
        // Avoid duplicate demo data when seeder is executed multiple times.
        if (StreetFood::query()->exists()) {
            $this->command?->info('Street foods already seeded, skipping.');
            return;
        }

        $streetFoods = [
            [
                'name' => 'Nasi Goreng',
                'name_en' => 'Fried Rice',
                'description' => 'Nasi yang digoreng dengan bumbu kecap, bawang, dan berbagai topping',
                'category' => 'Nasi',
                'common_ingredients' => ['nasi', 'kecap manis', 'bawang merah', 'bawang putih', 'telur', 'minyak goreng', 'sayuran'],
                'ai_keywords' => ['nasi', 'goreng', 'rice', 'fried', 'nasgor', 'nasi goreng']
            ],
            [
                'name' => 'Ayam Goreng',
                'name_en' => 'Fried Chicken',
                'description' => 'Ayam yang digoreng dengan bumbu rempah khas Indonesia',
                'category' => 'Ayam',
                'common_ingredients' => ['ayam', 'tepung', 'minyak goreng', 'bumbu ayam goreng', 'jeruk nipis'],
                'ai_keywords' => ['ayam', 'goreng', 'chicken', 'fried', 'ayam goreng']
            ],
            [
                'name' => 'Bakso',
                'name_en' => 'Meatballs',
                'description' => 'Bakso daging sapi dengan mie dan sayuran dalam kuah kaldu',
                'category' => 'Bakso',
                'common_ingredients' => ['daging sapi', 'tepung tapioka', 'mie', 'kuah kaldu', 'bawang', 'seledri'],
                'ai_keywords' => ['bakso', 'meatballs', 'daging', 'meat', 'bakso kuah']
            ],
            [
                'name' => 'Sate Ayam',
                'name_en' => 'Chicken Satay',
                'description' => 'Sate ayam dengan bumbu kacang dan lontong',
                'category' => 'Sate',
                'common_ingredients' => ['ayam', 'bumbu kacang', 'kecap', 'bawang', 'lontong', 'tusuk sate'],
                'ai_keywords' => ['sate', 'satay', 'ayam', 'chicken', 'bumbu kacang']
            ],
            [
                'name' => 'Gado-Gado',
                'name_en' => 'Vegetable Salad with Peanut Sauce',
                'description' => 'Sayuran rebus dengan bumbu kacang dan kerupuk',
                'category' => 'Sayuran',
                'common_ingredients' => ['kentang', 'tempe', 'tahu', 'kacang panjang', 'bumbu kacang', 'kerupuk'],
                'ai_keywords' => ['gado-gado', 'salad', 'sayuran', 'vegetable', 'bumbu kacang']
            ],
            [
                'name' => 'Rendang',
                'name_en' => 'Beef Rendang',
                'description' => 'Daging sapi yang dimasak dengan santan dan rempah-rempah khas Padang',
                'category' => 'Daging',
                'common_ingredients' => ['daging sapi', 'santan', 'cabai', 'bawang', 'jahe', 'lengkuas', 'rempah-rempah'],
                'ai_keywords' => ['rendang', 'beef', 'daging', 'santan', 'padang']
            ],
            [
                'name' => 'Soto Ayam',
                'name_en' => 'Chicken Soto',
                'description' => 'Sup ayam dengan kuah kuning dan berbagai pelengkap',
                'category' => 'Soto',
                'common_ingredients' => ['ayam', 'santan', 'kunyit', 'bawang', 'mie', 'tauge', 'telur'],
                'ai_keywords' => ['soto', 'ayam', 'chicken', 'sup', 'kuah kuning']
            ],
            [
                'name' => 'Mie Ayam',
                'name_en' => 'Chicken Noodles',
                'description' => 'Mie dengan topping ayam cincang dan sayuran',
                'category' => 'Mie',
                'common_ingredients' => ['mie', 'ayam', 'sayuran', 'minyak ayam', 'bawang', 'kecap'],
                'ai_keywords' => ['mie', 'ayam', 'noodles', 'chicken', 'mie ayam']
            ],
            [
                'name' => 'Martabak Manis',
                'name_en' => 'Sweet Martabak',
                'description' => 'Martabak dengan topping cokelat, keju, dan kacang',
                'category' => 'Martabak',
                'common_ingredients' => ['tepung', 'telur', 'gula', 'cokelat', 'keju', 'kacang', 'margarin'],
                'ai_keywords' => ['martabak', 'manis', 'sweet', 'cokelat', 'chocolate']
            ],
            [
                'name' => 'Pisang Goreng',
                'name_en' => 'Fried Banana',
                'description' => 'Pisang yang digoreng dengan balutan tepung',
                'category' => 'Buah',
                'common_ingredients' => ['pisang', 'tepung', 'minyak goreng', 'gula'],
                'ai_keywords' => ['pisang', 'goreng', 'banana', 'fried', 'pisang goreng']
            ]
        ];

        foreach ($streetFoods as $foodData) {
            // Get AI analysis for nutrition and halal status
            $aiAnalysis = $this->aiService->analyzeStreetFood(
                $foodData['name'],
                $foodData['description'],
                $foodData['common_ingredients']
            );

            $nutrition = $aiAnalysis['nutrition_per_serving'] ?? [
                'serving_size_g' => 150,
                'calories' => 250,
                'protein_g' => 8,
                'carbs_g' => 30,
                'fat_g' => 12,
                'fiber_g' => 2,
                'sugar_g' => 5,
                'sodium_mg' => 600
            ];

            $streetFood = StreetFood::create([
                'name' => $foodData['name'],
                'name_en' => $foodData['name_en'],
                'slug' => \Str::slug($foodData['name']),
                'description' => $foodData['description'],
                'category' => $foodData['category'],
                'calories_min' => max(0, $nutrition['calories'] - 50),
                'calories_max' => $nutrition['calories'] + 50,
                'calories_typical' => $nutrition['calories'],
                'protein' => $nutrition['protein_g'] ?? 8,
                'carbs' => $nutrition['carbs_g'] ?? 30,
                'fat' => $nutrition['fat_g'] ?? 12,
                'fiber' => $nutrition['fiber_g'] ?? 2,
                'sugar' => $nutrition['sugar_g'] ?? 5,
                'sodium' => $nutrition['sodium_mg'] ?? 600,
                'serving_size_grams' => $nutrition['serving_size_g'] ?? 150,
                'serving_description' => '1 porsi',
                'halal_status' => $aiAnalysis['halal_status'] ?? 'halal',
                'halal_notes' => implode(', ', $aiAnalysis['halal_concerns'] ?? ['Umumnya halal dengan bahan standar']),
                'health_tags' => $this->generateHealthTags($nutrition),
                'health_notes' => $aiAnalysis['recommendations'] ?? 'Konsumsi seimbang dan proporsi yang tepat',
                'ai_keywords' => $foodData['ai_keywords'],
                'common_ingredients' => $foodData['common_ingredients'],
                'is_popular' => true,
                'is_active' => true,
                'ai_analysis_data' => json_encode($aiAnalysis)
            ]);

            // Create variants for each food
            $this->createFoodVariants($streetFood, $foodData['common_ingredients']);
        }

        $this->command?->info('Street foods seeded with AI analysis successfully.');
    }

    private function generateHealthTags(array $nutrition): array
    {
        $tags = [];

        if (($nutrition['calories'] ?? 0) > 400) {
            $tags[] = 'tinggi_kalori';
        }
        if (($nutrition['protein_g'] ?? 0) > 15) {
            $tags[] = 'tinggi_protein';
        }
        if (($nutrition['carbs_g'] ?? 0) > 40) {
            $tags[] = 'tinggi_karbohidrat';
        }
        if (($nutrition['fat_g'] ?? 0) > 15) {
            $tags[] = 'tinggi_lemak';
        }
        if (($nutrition['fiber_g'] ?? 0) > 5) {
            $tags[] = 'tinggi_serabut';
        }
        if (($nutrition['sodium_mg'] ?? 0) > 1000) {
            $tags[] = 'tinggi_natrium';
        }
        if (($nutrition['sugar_g'] ?? 0) > 10) {
            $tags[] = 'tinggi_gula';
        }

        return $tags;
    }

    private function createFoodVariants(StreetFood $streetFood, array $baseIngredients): void
    {
        $variants = [
            'Kecil' => ['multiplier' => 0.7, 'price_adjustment' => -2000],
            'Sedang' => ['multiplier' => 1.0, 'price_adjustment' => 0],
            'Besar' => ['multiplier' => 1.3, 'price_adjustment' => 3000],
            'Spesial' => ['multiplier' => 1.5, 'price_adjustment' => 5000, 'extra_ingredients' => ['telur', 'kornet']]
        ];

        foreach ($variants as $variantName => $variantData) {
            $ingredients = $baseIngredients;
            if (isset($variantData['extra_ingredients'])) {
                $ingredients = array_merge($ingredients, $variantData['extra_ingredients']);
            }

            FoodVariant::create([
                'street_food_id' => $streetFood->id,
                'variant_name' => $variantName,
                'variant_type' => 'size',
                'name' => $variantName,
                'description' => "Varian {$variantName} untuk {$streetFood->name}",
                'ingredients' => json_encode($ingredients),
                'calories' => (int)($streetFood->calories_typical * $variantData['multiplier']),
                'calories_modifier' => (int)($streetFood->calories_typical * $variantData['multiplier']),
                'price_adjustment' => $variantData['price_adjustment'],
                'price_modifier' => $variantData['price_adjustment'],
                'serving_size_grams' => (int)($streetFood->serving_size_grams * $variantData['multiplier']),
                'is_available' => true,
                'is_default' => $variantName === 'Sedang',
            ]);
        }
    }
}
