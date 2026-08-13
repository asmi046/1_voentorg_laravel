<?php

namespace App\DTO;

use Illuminate\Http\Request;

final readonly class CheckoutData
{
    /**
     * @param  array<int, CheckoutItemData>  $items
     */
    public function __construct(
        public string $name,
        public ?string $email,
        public string $phone,
        public ?string $comment,
        public ?string $promo_code,
        public ?float $discount,
        public DeliveryData $delivery,
        public array $items,
        public string $session_id,
        public ?int $user_id,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $validated = $request->validated();

        $items = array_map(
            static fn (array $item) => CheckoutItemData::fromArray($item),
            $validated['items'] ?? [],
        );

        return new self(
            name: (string) ($validated['name'] ?? ''),
            email: $validated['email'] ?? null,
            phone: (string) ($validated['phone'] ?? ''),
            comment: $validated['comment'] ?? null,
            promo_code: $validated['promo_code'] ?? null,
            discount: isset($validated['discount']) ? (float) $validated['discount'] : null,
            delivery: DeliveryData::fromArray($validated['delivery'] ?? []),
            items: $items,
            session_id: $request->session()->getId(),
            user_id: $request->user()?->id,
        );
    }
}
