<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayOrderConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $order_number;

    public Order $request;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $request, string $order_number)
    {
        $this->order_number = $order_number;
        $this->request = $request;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
