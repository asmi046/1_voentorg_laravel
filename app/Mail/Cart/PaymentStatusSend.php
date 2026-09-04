<?php

namespace App\Mail\Cart;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusSend extends Mailable
{
    use Queueable, SerializesModels;

    protected int $orderId;

    protected string $paymentId;

    protected string $orderStatus;

    protected string $amount;

    protected ?ShopOrder $shopOrder;

    public function __construct($orderId, $paymentId, $orderStatus, $amount, ?ShopOrder $shopOrder = null)
    {
        $this->orderId = (int) $orderId;
        $this->paymentId = (string) $paymentId;
        $this->orderStatus = (string) $orderStatus;
        $this->amount = number_format((float) $amount, 2, '.', ' ');
        $this->shopOrder = $shopOrder;
    }

    public function build()
    {
        return $this->from(config('cart.send_from'), config('cart.send_from_text'))
            ->subject('Статус оплаты заказа №'.$this->orderId)
            ->replyTo(config('cart.reply_to'), config('cart.reply_to_text'))
            ->view('mail.payment_status')
            ->with([
                'orderId' => $this->orderId,
                'paymentId' => $this->paymentId,
                'orderStatus' => $this->orderStatus,
                'amount' => $this->amount,
                'shopOrder' => $this->shopOrder,
            ]);
    }
}
