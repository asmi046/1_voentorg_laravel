<?php

namespace App\Services\Import;

class MatchResult
{
    public function __construct(
        public readonly array $offers,
        public readonly string $method,
        public readonly float $score,
        public readonly ?string $matchedBaseName = null,
    ) {}

    public function isAmbiguous(?MatchResult $other = null): bool
    {
        return $this->method === 'fuzzy' && $this->score < 0.85;
    }
}
