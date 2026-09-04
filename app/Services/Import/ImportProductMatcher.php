<?php

namespace App\Services\Import;

use App\Models\Product;

class ImportProductMatcher
{
    public function __construct(
        private NameNormalizer $normalizer,
        private TitleSimilarity $similarity,
        private float $fuzzyThreshold = 0.7,
        private float $autoAcceptThreshold = 0.85,
    ) {}

    public function fuzzyThreshold(): float
    {
        return $this->fuzzyThreshold;
    }

    public function autoAccept(): float
    {
        return $this->autoAcceptThreshold;
    }

    public function findCandidates(Product $product, XmlImportIndex $index, array $categoryExternalIds = []): ?MatchResult
    {
        $productNorm = $this->normalizer->normalize($product->title);

        if ($product->external_id && isset($index->byExternalId[$product->external_id])) {
            $hit = $index->byExternalId[$product->external_id];
            $groupKey = $hit['base_norm'];

            return new MatchResult(
                offers: $index->byNormBase[$groupKey] ?? [$hit],
                method: 'external_id',
                score: 1.0,
                matchedBaseName: $hit['base_name'],
            );
        }

        if (isset($index->byNormBase[$productNorm])) {
            return new MatchResult(
                offers: $index->byNormBase[$productNorm],
                method: 'exact',
                score: 1.0,
                matchedBaseName: $product->title,
            );
        }

        $pool = $this->collectPool($index, $categoryExternalIds);

        $scored = [];
        foreach ($pool as $offer) {
            $score = $this->similarity->combined($product->title, $offer['base_name'], $this->normalizer);
            if ($score >= 0.5) {
                $scored[$offer['base_norm']][] = ['offer' => $offer, 'score' => $score];
            }
        }

        if (! $scored) {
            return null;
        }

        foreach ($scored as $baseNorm => $list) {
            usort($list, fn ($a, $b) => $b['score'] <=> $a['score']);
            $scored[$baseNorm] = $list;
        }

        $flat = [];
        foreach ($scored as $list) {
            foreach ($list as $entry) {
                $flat[] = $entry;
            }
        }
        usort($flat, fn ($a, $b) => $b['score'] <=> $a['score']);

        $best = $flat[0];
        if ($best['score'] < $this->fuzzyThreshold) {
            return null;
        }

        $offers = $index->byNormBase[$best['offer']['base_norm']] ?? [$best['offer']];

        return new MatchResult(
            offers: $offers,
            method: 'fuzzy',
            score: $best['score'],
            matchedBaseName: $best['offer']['base_name'],
        );
    }

    private function collectPool(XmlImportIndex $index, array $categoryExternalIds): array
    {
        if (! $categoryExternalIds) {
            $pool = [];
            foreach ($index->byNormBase as $list) {
                foreach ($list as $offer) {
                    $pool[] = $offer;
                }
            }

            return $pool;
        }

        $pool = [];
        foreach ($categoryExternalIds as $catId) {
            if (isset($index->byCategory[$catId])) {
                foreach ($index->byCategory[$catId] as $offer) {
                    $pool[$offer['base_norm']] = $offer;
                }
            }
        }

        return array_values($pool);
    }
}
