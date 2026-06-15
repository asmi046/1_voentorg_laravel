<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BascetOrderCreated
{
    use Dispatchable, SerializesModels;

    public int $orderId;

    public array $formData;

    public function __construct(int $orderId, array $formData)
    {
        $this->orderId = $orderId;
        $this->formData = $formData;
    }
}
