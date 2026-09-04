<?php

namespace App\Services\Import;

class NameNormalizer
{
    public function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value), 'UTF-8');
        $value = preg_replace('/\s+/u', ' ', $value);
        $value = str_replace(['ё', 'Ё'], ['е', 'Е'], $value);

        return trim((string) $value);
    }

    public function words(string $value): array
    {
        $normalized = $this->normalize($value);
        if ($normalized === '') {
            return [];
        }
        $tokens = preg_split('/[\s,.()"\']+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_unique(array_filter($tokens, fn ($t) => mb_strlen($t) > 1)));
    }
}
