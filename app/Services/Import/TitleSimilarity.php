<?php

namespace App\Services\Import;

class TitleSimilarity
{
    public function jaccard(string $a, string $b, NameNormalizer $normalizer): float
    {
        $wa = $normalizer->words($a);
        $wb = $normalizer->words($b);
        if (! $wa || ! $wb) {
            return 0.0;
        }
        $inter = count(array_intersect($wa, $wb));
        $union = count(array_unique(array_merge($wa, $wb)));

        return $union > 0 ? $inter / $union : 0.0;
    }

    public function levenshteinWords(string $a, string $b, NameNormalizer $normalizer): float
    {
        $wa = $normalizer->words($a);
        $wb = $normalizer->words($b);
        if (! $wa || ! $wb) {
            return 0.0;
        }

        $matched = 0;
        $usedB = [];
        foreach ($wa as $i => $tokenA) {
            $best = null;
            $bestDist = PHP_INT_MAX;
            foreach ($wb as $j => $tokenB) {
                if (isset($usedB[$j])) {
                    continue;
                }
                $d = levenshtein($tokenA, $tokenB);
                if ($d < $bestDist) {
                    $bestDist = $d;
                    $best = $j;
                }
            }
            if ($best !== null && $bestDist <= max(1, (int) floor(mb_strlen($tokenA) / 3))) {
                $matched++;
                $usedB[$best] = true;
            }
        }

        $total = max(count($wa), count($wb));

        return $total > 0 ? $matched / $total : 0.0;
    }

    public function combined(string $a, string $b, NameNormalizer $normalizer): float
    {
        return 0.5 * $this->jaccard($a, $b, $normalizer)
             + 0.5 * $this->levenshteinWords($a, $b, $normalizer);
    }
}
