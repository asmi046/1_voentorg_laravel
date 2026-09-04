<?php

namespace App\Services\Import;

class ModifierExtractor
{
    private array $patterns = [
        'size_extended_slash' => '/(?<![\p{L}])\s*р-р\.\s*([0-9]+(?:[\/\-][0-9]+(?:\s+[0-9]+\/[0-9]+)?)?)\s*$/iu',
        'size_extended_dash' => '/(?<![\p{L}])\s*р-р\.\s*([0-9]+(?:\-[0-9]+)?)\s*$/iu',
        'size_simple' => '/(?<![\p{L}])\s*р-р\.\s*([0-9]+)\s*$/iu',
        'size_letter_dot' => '/(?<![\p{L}])\s*р\.\s*([0-9]+(?:[\/\-][0-9]+(?:\s+[0-9]+\/[0-9]+)?)?)\s*$/iu',
        'size_only' => '/(?<![\p{L}])\s*р\.\s*([0-9]+)\s*$/iu',
        'razmer_word' => '/(?<![\p{L}])\s*размер[:\s]+([^\s,]+(?:\s*[-–]\s*[^\s,]+)?)\s*$/iu',
        'rost_word' => '/(?<![\p{L}])\s*рост[:\s]+([0-9]+)\s*$/iu',
    ];

    public function split(string $title): array
    {
        $title = rtrim($title);

        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $title, $m)) {
                $modifier = trim($m[1]);
                $base = trim(preg_replace($pattern, '', $title));

                return [
                    'base_name' => $base,
                    'modifier' => $modifier,
                ];
            }
        }

        return [
            'base_name' => $title,
            'modifier' => '',
        ];
    }
}
