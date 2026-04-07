<?php

namespace App\Helpers;

class WordFilterHelper
{
    private static array $bannedWords = [
        'bangsat',
        'anjing',
        'babi',
        'goblok',
        'tolol',
        'kafir',
        'sesat',
    ];

    public static function filter(string $content): string
    {
        foreach (self::$bannedWords as $word) {
            $replacement = str_repeat('*', mb_strlen($word));
            $content = preg_replace(
                '/\b' . preg_quote($word, '/') . '\b/iu',
                $replacement,
                $content
            ) ?? $content;
        }

        return $content;
    }
}
