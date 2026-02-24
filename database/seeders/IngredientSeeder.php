<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Models\HalalCriticalIngredient;

class IngredientSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed from HalalCriticalIngredient (Existing logic)
        $criticals = HalalCriticalIngredient::where('active', true)->get();
        foreach ($criticals as $c) {
            Ingredient::updateOrCreate(
                ['name' => $c->name],
                [
                    'e_number' => null,
                    'halal_status' => $c->status ?? 'unknown',
                    'health_risk' => 'safe',
                    'description' => $c->description,
                    'sources' => $c->common_sources,
                    'notes' => trim((string)($c->critical_reason ?? ''))
                        . ((empty($c->alternatives)) ? '' : "\nAlternatives: {$c->alternatives}"),
                    'active' => (bool) $c->active,
                ]
            );
        }

        // 2. Comprehensive E-Number Database (COLORS, PRESERVATIVES, ETC.)
        $eNumbers = [
            // Colors
            ['E100', 'Curcumin', 'halal', 'safe', 'Natural yellow color from turmeric.', 'Plant'],
            ['E101', 'Riboflavin', 'halal', 'safe', 'Vitamin B2, yellow color.', 'Synthesized/Plant'],
            ['E102', 'Tartrazine', 'halal', 'high_risk', 'Synthetic yellow azo dye. May cause allergic reactions in some people.', 'Synthetic'],
            ['E104', 'Quinoline Yellow', 'halal', 'high_risk', 'Synthetic yellow dye. Banned in some countries (US, Japan).', 'Synthetic'],
            ['E110', 'Sunset Yellow FCF', 'halal', 'high_risk', 'Synthetic orange dye. May cause allergic reactions.', 'Synthetic'],
            ['E120', 'Cochineal / Carmine', 'haram', 'low_risk', 'Red color extracted from crushed insects (Dactylopius coccus).', 'Insect'],
            ['E122', 'Azorubine', 'halal', 'high_risk', 'Synthetic red dye.', 'Synthetic'],
            ['E123', 'Amaranth', 'halal', 'high_risk', 'Synthetic red dye. Prohibited in USA.', 'Synthetic'],
            ['E124', 'Ponceau 4R', 'halal', 'high_risk', 'Synthetic red dye.', 'Synthetic'],
            ['E127', 'Erythrosine', 'halal', 'low_risk', 'Synthetic red dye (used in cherries).', 'Synthetic'],
            ['E129', 'Allura Red AC', 'halal', 'low_risk', 'Synthetic red dye.', 'Synthetic'],
            ['E133', 'Brilliant Blue FCF', 'halal', 'low_risk', 'Synthetic blue dye.', 'Synthetic'],
            ['E140', 'Chlorophylls', 'halal', 'safe', 'Green color from plants.', 'Plant'],
            ['E141', 'Copper complexes of chlorophylls', 'halal', 'safe', 'Stable green color.', 'Plant/Synthetic'],
            ['E150a', 'Plain Caramel', 'halal', 'safe', 'Brown color from burnt sugar.', 'Plant'],
            ['E150d', 'Sulphite ammonia caramel', 'halal', 'low_risk', 'Caramel color, contains sulphites.', 'Plant/Synthetic'],
            ['E153', 'Vegetable Carbon', 'halal', 'safe', 'Black color from burnt vegetable matter.', 'Plant'],
            ['E160a', 'Carorines (Beta-carotene)', 'halal', 'safe', 'Orange color, Vitamin A precursor. Usually plant-based but check carrier (gelatin).', 'Plant/Synthetic'],
            ['E160b', 'Annatto', 'halal', 'low_risk', 'Natural orange-red color from seeds.', 'Plant'],
            ['E162', 'Beetroot Red', 'halal', 'safe', 'Red color from beets.', 'Plant'],
            ['E163', 'Anthocyanins', 'halal', 'safe', 'Red/purple/blue colors from skins of fruit.', 'Plant'],
            ['E171', 'Titanium Dioxide', 'halal', 'high_risk', 'White pigment. Banned in EU food, allowed elsewhere.', 'Mineral'],
            ['E172', 'Iron Oxides', 'halal', 'safe', 'Red, yellow, black pigments.', 'Mineral'],

            // Preservatives
            ['E200', 'Sorbic Acid', 'halal', 'safe', 'Antimicrobial preservative.', 'Synthetic/Plant'],
            ['E202', 'Potassium Sorbate', 'halal', 'safe', 'Potassium salt of sorbic acid.', 'Synthetic'],
            ['E210', 'Benzoic Acid', 'halal', 'low_risk', 'Preservative against yeasts/moulds.', 'Synthetic'],
            ['E211', 'Sodium Benzoate', 'halal', 'low_risk', 'Common preservative. Can form benzene if mixed with Vit C.', 'Synthetic'],
            ['E220', 'Sulphur Dioxide', 'halal', 'high_risk', 'Preservative and antioxidant. Allergen.', 'Synthetic'],
            ['E250', 'Sodium Nitrite', 'halal', 'high_risk', 'Used in cured meats. Linked to health risks in large amounts.', 'Synthetic'],
            ['E251', 'Sodium Nitrate', 'halal', 'low_risk', 'Preservative in meats.', 'Mineral/Synthetic'],
            ['E260', 'Acetic Acid', 'halal', 'safe', 'Vinegar acid.', 'Natural/Synthetic'],
            ['E270', 'Lactic Acid', 'halal', 'safe', 'Acidity regulator. Usually from bacterial fermentation of sugar.', 'Microbial'],
            ['E296', 'Malic Acid', 'halal', 'safe', 'Acid found in apples.', 'Plant/Synthetic'],

            // Antioxidants & Regulators
            ['E300', 'Ascorbic Acid (Vitamin C)', 'halal', 'safe', 'Antioxidant.', 'Plant/Synthetic'],
            ['E322', 'Lecithins', 'halal', 'safe', 'Emulsifier. Usually soy (halal). If from animal likely haram/syubhat.', 'Plant (Soy/Sunflower)'],
            ['E330', 'Citric Acid', 'halal', 'safe', 'Acidifier.', 'Microbial/Plant'],
            ['E331', 'Sodium Citrates', 'halal', 'safe', 'Acidity regulator.', 'Synthetic'],
            ['E339', 'Sodium Phosphates', 'halal', 'low_risk', 'Emulsifier/Stabilizer.', 'Mineral'],

            // Thickeners, Stabilizers, Emulsifiers
            ['E400', 'Alginic Acid', 'halal', 'safe', 'Thickener from seaweed.', 'Plant'],
            ['E401', 'Sodium Alginate', 'halal', 'safe', 'Thickener from seaweed.', 'Plant'],
            ['E406', 'Agar', 'halal', 'safe', 'Vegetable gelatin substitute.', 'Plant (Seaweed)'],
            ['E407', 'Carrageenan', 'halal', 'low_risk', 'Thickener from seaweed. Linked to inflammation in some studies.', 'Plant'],
            ['E410', 'Locust Bean Gum', 'halal', 'safe', 'Thickener from carob seeds.', 'Plant'],
            ['E412', 'Guar Gum', 'halal', 'safe', 'Thickener from guar beans.', 'Plant'],
            ['E414', 'Gum Arabic', 'halal', 'safe', 'Stabilizer from acacia tree.', 'Plant'],
            ['E415', 'Xanthan Gum', 'halal', 'safe', 'Thickener from bacterial fermentation.', 'Microbial'],
            ['E420', 'Sorbitol', 'halal', 'low_risk', 'Sweetener/Humectant. Excessive use causes laxative effect.', 'Plant/Synthetic'],
            ['E422', 'Glycerol / Glycerin', 'syubhat', 'safe', 'Humectant. Can be animal or plant derived. Verify source.', 'Plant/Animal/Synthetic'],
            ['E440', 'Pectins', 'halal', 'safe', 'Gelling agent from fruit.', 'Plant'],
            ['E441', 'Gelatin', 'syubhat', 'safe', 'Gelling agent from animal collagen/bones. Halal only if from halal-slaughtered animals.', 'Animal'],
            ['E450', 'Diphosphates', 'halal', 'low_risk', 'Emulsifier.', 'Mineral'],
            ['E460', 'Cellulose', 'halal', 'safe', 'Bulking agent from wood pulp/cotton.', 'Plant'],
            ['E466', 'CMC (Carboxymethylcellulose)', 'halal', 'safe', 'Thickener.', 'Plant/Synthetic'],
            ['E471', 'Mono- and diglycerides of fatty acids', 'syubhat', 'safe', 'Emulsifier. Made from fats. Can be animal (pork/beef) or plant based.', 'Plant/Animal'],
            ['E472e', 'DATEM', 'syubhat', 'safe', 'Emulsifier in bread. Derived from fatty acids.', 'Plant/Animal'],
            ['E476', 'PGPR (Polyglycerol polyricinoleate)', 'halal', 'safe', 'Emulsifier in chocolate. Plant based (Castor oil).', 'Plant'],

            // Others
            ['E500', 'Sodium Carbonates (Baking Soda)', 'halal', 'safe', 'Raising agent.', 'Mineral'],
            ['E503', 'Ammonium Carbonates', 'halal', 'safe', 'Raising agent.', 'Mineral'],
            ['E551', 'Silicon Dioxide', 'halal', 'safe', 'Anti-caking agent.', 'Mineral'],
            ['E621', 'Monosodium Glutamate (MSG)', 'halal', 'low_risk', 'Flavor enhancer. Fermented from starch/sugar.', 'Microbial'],
            ['E627', 'Disodium Guanylate', 'syubhat', 'safe', 'Flavor enhancer. Often used with MSG. Can be from fish or meat.', 'Microbial/Animal'],
            ['E631', 'Disodium Inosinate', 'syubhat', 'safe', 'Flavor enhancer. Often from meat/fish (especially sardines/pork).', 'Animal/Microbial'],
            ['E901', 'Beeswax', 'halal', 'safe', 'Glazing agent.', 'Insect (Honeybee)'],
            ['E903', 'Carnauba Wax', 'halal', 'safe', 'Glazing agent from palm leaves.', 'Plant'],
            ['E904', 'Shellac', 'halal', 'safe', 'Resin from lac bug. Generally accepted as Halal (like honey).', 'Insect'],
            ['E920', 'L-Cysteine', 'syubhat', 'safe', 'Dough conditioner. Can be from human hair, duck feathers, or synthetic.', 'Animal/Synthetic/Human'],
            ['E950', 'Acesulfame K', 'halal', 'low_risk', 'Artificial sweetener.', 'Synthetic'],
            ['E951', 'Aspartame', 'halal', 'low_risk', 'Artificial sweetener (Nutrasweet).', 'Synthetic'],
            ['E954', 'Saccharin', 'halal', 'low_risk', 'Artificial sweetener.', 'Synthetic'],
            ['E955', 'Sucralose', 'halal', 'safe', 'Artificial sweetener (Splenda).', 'Synthetic']
        ];

        foreach ($eNumbers as $data) {
            $eNumber = $data[0];
            $name = $data[1];
            
            // Check existence by E-number OR Name to avoid unique constraint violations
            $ingredient = Ingredient::where('e_number', $eNumber)
                                  ->orWhere('name', $name)
                                  ->first();

            $attributes = [
                'e_number' => $eNumber,
                'name' => $name, // Ensure name is normalized
                'halal_status' => $data[2],
                'health_risk' => $data[3],
                'description' => $data[4],
                'sources' => $data[5],
                'active' => true,
                'notes' => 'Generated by comprehensive seeder.'
            ];

            if ($ingredient) {
                $ingredient->update($attributes);
            } else {
                Ingredient::create($attributes);
            }
        }
    }
}
