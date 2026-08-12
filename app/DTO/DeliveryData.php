<?php

namespace App\DTO;

final readonly class DeliveryData
{
    public function __construct(
        public string $provider,
        public string $method,
        public ?string $price,
        public ?string $tariff,
        public ?array $delivery_date_range,
        public ?string $city,
        public ?string $pickup_point_id,
        public ?string $pickup_point_address,
        public ?string $delivery_address,
        public ?string $apartment,
        public ?array $raw_data,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            provider: (string) ($data['provider'] ?? ''),
            method: (string) ($data['method'] ?? ''),
            price: isset($data['price']) ? (string) $data['price'] : null,
            tariff: $data['tariff'] ?? null,
            delivery_date_range: $data['delivery_date_range'] ?? null,
            city: $data['city'] ?? null,
            pickup_point_id: $data['pickup_point_id'] ?? null,
            pickup_point_address: $data['pickup_point_address'] ?? null,
            delivery_address: $data['delivery_address'] ?? null,
            apartment: $data['apartment'] ?? null,
            raw_data: $data['raw_data'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return [
            'provider' => $this->provider,
            'method' => $this->method,
            'price' => $this->price,
            'tariff' => $this->tariff,
            'delivery_date_range' => $this->delivery_date_range,
            'city' => $this->city,
            'pickup_point_id' => $this->pickup_point_id,
            'pickup_point_address' => $this->pickup_point_address,
            'delivery_address' => $this->delivery_address,
            'apartment' => $this->apartment,
            'raw_data' => $this->raw_data,
        ];
    }
}
