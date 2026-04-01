<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiPrompt extends Model
{
    protected $fillable = [
        'feature_key', 'feature_name', 'system_prompt',
        'user_prompt_template', 'temperature', 'max_tokens',
        'model', 'is_active', 'version', 'notes',
    ];

    protected $casts = [
        'temperature' => 'float',
        'max_tokens'  => 'integer',
        'is_active'   => 'boolean',
        'version'     => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Get the active prompt for a given feature, with optional fallback. */
    public static function forFeature(string $featureKey): ?self
    {
        return static::where('feature_key', $featureKey)
                     ->active()
                     ->first();
    }

    /** Build a user prompt from the template by replacing placeholders. */
    public function buildUserPrompt(array $variables = []): string
    {
        $template = $this->user_prompt_template ?? '';

        foreach ($variables as $key => $value) {
            $template = str_replace("{{$key}}", (string) $value, $template);
        }

        return $template;
    }

    /** Increment version when updating the prompt. */
    public function bumpVersion(): void
    {
        $this->increment('version');
    }
}
