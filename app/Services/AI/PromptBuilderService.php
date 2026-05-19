<?php

namespace App\Services\AI;

use App\Models\AiPrompt;
use Illuminate\Support\Facades\Log;

class PromptBuilderService
{
    /** Maps logical prompt types to ai_prompts.feature_key */
    private const TYPE_MAP = [
        'food_analysis' => 'food_analysis',
        'halal_check' => 'halal_check',
        'health_check' => 'health_check',
        'user_chat' => 'user_chat',
        'product_comparison' => 'product_comparison',
        'risk_analysis' => 'risk_analysis',
        'recommendation' => 'recommendation',
    ];

    /**
     * Build a full prompt string from DB template or fallback, with variable injection.
     *
     * @param  array<string, mixed>  $variables
     */
    public function build(string $type, array $variables = [], ?string $fallbackTemplate = null): string
    {
        $featureKey = self::TYPE_MAP[$type] ?? $type;
        $record = AiPrompt::forFeature($featureKey);

        $template = $record?->system_prompt
            ?? $fallbackTemplate
            ?? $this->defaultTemplate($type);

        if ($record && $record->user_prompt_template) {
            $userPart = $record->buildUserPrompt($variables);
            if (trim($userPart) !== '') {
                $template = trim($template) . "\n\n" . $userPart;
            }
        }

        return $this->injectVariables($template, $variables);
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    private function injectVariables(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            $template = str_replace(
                ['{' . $key . '}', '{{' . $key . '}}'],
                (string) $value,
                $template
            );
        }

        return $template;
    }

    private function defaultTemplate(string $type): string
    {
        return match ($type) {
            'user_chat' => 'Anda adalah AI Halalytics. Jawab dengan jelas dan personal. Jangan gunakan kalimat placeholder.',
            'food_analysis' => 'Anda adalah AI Halalytics. Analisis produk makanan/minuman: halal, gizi, dan risiko kesehatan berbasis bukti.',
            default => 'Anda adalah AI Halalytics — asisten kesehatan dan halal berbasis bukti ilmiah.',
        };
    }
}
