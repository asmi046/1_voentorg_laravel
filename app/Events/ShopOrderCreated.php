<?php

namespace App\Events;

use App\Models\ShopOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShopOrderCreated
{
    use Dispatchable, SerializesModels;

    public int $orderId;
    public ShopOrder $order;

    public function __construct(int $orderId, ShopOrder $order)
    {
        $this->orderId = $orderId;
        $this->order = $order;
    }
}
