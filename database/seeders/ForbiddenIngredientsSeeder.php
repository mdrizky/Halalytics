<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForbiddenIngredient;

class ForbiddenIngredientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ingredients = [
            // HALAL / HARAM ISSUES
            [
                'name' => 'Pork',
                'code' => null,
                'aliases' => ['Babi', 'Porcine', 'Lard', 'Ham', 'Bacon', 'Sow'],
                'type' => 'halal_haram',
                'risk_level' => 'high',
                'reason' => 'Haram (Forbidden in Islam)',
                'description' => 'Meat and products derived from pigs.',
                'source' => 'Quran'
            ],
            [
                'name' => 'Carmine',
                'code' => 'E120',
                'aliases' => ['Cochineal', 'Carminic Acid', 'Natural Red 4', 'CI 75470'],
                'type' => 'halal_haram',
                'risk_level' => 'medium',
                'reason' => 'Syubhat (Controversial, often considered Haram if from insects)',
                'description' => 'Red pigment derived from the cochineal insect.',
                'source' => 'MUI'
            ],
            [
                'name' => 'Alcohol',
                'code' => null,
                'aliases' => ['Ethanol', 'Alkohol', 'Wine', 'Beer', 'Rum', 'Brandy'],
                'type' => 'halal_haram',
                'risk_level' => 'high',
                'reason' => 'Khamr (Intoxicant)',
                'description' => 'Alcoholic beverages and intoxicants.',
                'source' => 'Quran'
            ],
            [
                'name' => 'Gelatin',
                'code' => 'E441',
                'aliases' => ['Gelatine'],
                'type' => 'halal_haram',
                'risk_level' => 'medium',
                'reason' => 'Syubhat (Source could be pig or non-slaughtered animal)',
                'description' => 'Translucent, colorless, flavorless food ingredient.',
                'source' => 'General'
            ],

            // HEALTH HAZARDS (Example)
            [
                'name' => 'Hydroquinone',
                'code' => null,
                'aliases' => ['1,4-Benzenediol', 'Quinol'],
                'type' => 'health_hazard',
                'risk_level' => 'high',
                'reason' => 'Carcinogenic / Skin Damage',
                'description' => 'Skin lightening agent banned in many countries due to safety concerns.',
                'source' => 'BPOM'
            ],
            [
                'name' => 'Mercury',
                'code' => null,
                'aliases' => ['Merkuri', 'Hydrargyrum', 'Hg'],
                'type' => 'health_hazard',
                'risk_level' => 'high',
                'reason' => 'Toxic Heavy Metal',
                'description' => 'High toxicity, causes kidney and nervous system damage.',
                'source' => 'BPOM'
            ],
            [
                'name' => 'Rhodamine B',
                'code' => null,
                'aliases' => ['CI 45170'],
                'type' => 'health_hazard',
                'risk_level' => 'high',
                'reason' => 'Carcinogenic Dye',
                'description' => 'Synthetic textile dye often misused in food.',
                'source' => 'BPOM'
            ],
            
            // ALLERGENS (Example)
            [
                'name' => 'Peanuts',
                'code' => null,
                'aliases' => ['Kacang Tanah', 'Groundnuts'],
                'type' => 'allergen',
                'risk_level' => 'medium',
                'reason' => 'Common Allergen',
                'description' => 'Severe allergen for many people.',
                'source' => 'General'
            ]
        ];

        foreach ($ingredients as $ing) {
            ForbiddenIngredient::updateOrCreate(
                ['name' => $ing['name']],
                $ing
            );
        }
    }
}
