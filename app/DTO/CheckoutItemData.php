<?php

namespace App\DTO;

final readonly class CheckoutItemData
{
    public function __construct(
        public string $product_sku,
        public int $quantity,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            product_sku: (string) ($data['product_sku'] ?? ''),
            quantity: (int) ($data['quantity'] ?? 1),
        );
    }
}
