<?php

namespace App\Listeners;

use App\Events\ShopOrderCreated;
use App\Services\CdekOrderBuilder;
use Illuminate\Support\Facades\Log;

class RegisterCdekOrder
{
    public function __construct(
        private CdekOrderBuilder $builder
    ) {}

    public function handle(ShopOrderCreated $event): void
    {
        $order = $event->order;

        if (! $order->delivery || $order->delivery->provider !== 'СДЭК') {
            return;
        }

        try {
            $result = $this->builder->createOrder($order);

            if ($result && isset($result['entity']['uuid'])) {
                Log::channel('sdek')->info('CDEK order registered', [
                    'order_id' => $order->id,
                    'cdek_uuid' => $result['entity']['uuid'],
                    'cdek_number' => $result['entity']['cdek_number'] ?? null,
                ]);

                $order->update([
                    'external_order_id' => $result['entity']['uuid'],
                ]);
            } else {
                Log::channel('sdek')->error('CDEK order registration failed', [
                    'order_id' => $order->id,
                    'response' => $result,
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('sdek')->error('CDEK order registration error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}