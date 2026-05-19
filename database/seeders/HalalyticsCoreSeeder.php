<?php

namespace Database\Seeders;

use App\Models\AiPrompt;
use App\Models\DonationCampaign;
use App\Models\Ingredient;
use App\Models\NutritionRule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HalalyticsCoreSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAiPrompts();
        $this->seedNutritionRules();
        $this->seedIngredients();
        $this->seedDonationCampaigns();
    }

    private function seedAiPrompts(): void
    {
        $prompts = [
            [
                'feature_key' => 'food_analysis',
                'feature_name' => 'Analisis Produk & Makanan',
                'system_prompt' => 'Kamu adalah AI Halalytics. Analisis halal dan kesehatan secara terpisah lalu laporkan bersama. Jangan gunakan placeholder. Jangan klaim Halal Resmi tanpa sertifikasi MUI/BPJPH.',
            ],
            [
                'feature_key' => 'user_chat',
                'feature_name' => 'Chat AI Pengguna',
                'system_prompt' => 'Kamu adalah AI Halalytics — asisten kesehatan, gizi, dan halal berbasis bukti. Jawab jelas, personal, dan informatif.',
            ],
            [
                'feature_key' => 'halal_check',
                'feature_name' => 'Cek Halal',
                'system_prompt' => 'Fokus pada status halal, syubhat, dan sumber bahan. Selalu ingatkan verifikasi sertifikasi resmi.',
            ],
        ];

        foreach ($prompts as $p) {
            AiPrompt::updateOrCreate(
                ['feature_key' => $p['feature_key']],
                array_merge($p, [
                    'user_prompt_template' => 'Data: {user_message}',
                    'temperature' => 0.4,
                    'max_tokens' => 2048,
                    'is_active' => true,
                ])
            );
        }
    }

    private function seedNutritionRules(): void
    {
        if (! \Schema::hasTable('nutrition_rules')) {
            return;
        }

        $rules = [
            ['rule_key' => 'sugar_high', 'label' => 'Gula Tinggi', 'threshold_value' => 20, 'unit' => 'g'],
            ['rule_key' => 'sodium_high', 'label' => 'Sodium Tinggi', 'threshold_value' => 600, 'unit' => 'mg'],
        ];

        foreach ($rules as $rule) {
            NutritionRule::updateOrCreate(['rule_key' => $rule['rule_key']], $rule);
        }
    }

    private function seedIngredients(): void
    {
        $items = [
            ['name' => 'Gelatin', 'e_number' => 'E441', 'halal_status' => 'syubhat', 'health_risk' => 'low_risk'],
            ['name' => 'Kafein', 'halal_status' => 'halal', 'health_risk' => 'low_risk', 'description' => 'Stimulan. Batasi jika hipertensi atau gangguan tidur.'],
            ['name' => 'Gula', 'halal_status' => 'halal', 'health_risk' => 'low_risk', 'description' => 'Tinggi gula berisiko metabolik jika berlebihan.'],
            ['name' => 'Karmin (E120)', 'e_number' => 'E120', 'halal_status' => 'haram', 'health_risk' => 'low_risk', 'description' => 'Karmin — sering dari serangga.'],
        ];

        foreach ($items as $item) {
            Ingredient::updateOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['active' => true])
            );
        }
    }

    private function seedDonationCampaigns(): void
    {
        if (! \Schema::hasTable('donation_campaigns')) {
            return;
        }

        DonationCampaign::updateOrCreate(
            ['slug' => 'stunting-zero-hunger'],
            [
                'title' => 'Zero Hunger — Cegah Stunting',
                'description' => 'Bantu program gizi anak dan edukasi pola makan halal sehat di daerah 3T.',
                'target_amount' => 50000000,
                'collected_amount' => 12500000,
                'donor_count' => 48,
                'category' => 'stunting',
                'is_active' => true,
                'is_urgent' => true,
            ]
        );

        DonationCampaign::updateOrCreate(
            ['slug' => 'halal-food-security'],
            [
                'title' => 'Keamanan Pangan Halal',
                'description' => 'Dukung verifikasi produk UMKM dan literasi label halal.',
                'target_amount' => 25000000,
                'collected_amount' => 8000000,
                'donor_count' => 22,
                'category' => 'pangan_halal',
                'is_active' => true,
            ]
        );
    }
}
