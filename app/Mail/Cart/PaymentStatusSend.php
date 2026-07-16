<?php

namespace App\Mail\Cart;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusSend extends Mailable
{
    use Queueable, SerializesModels;

    protected $orderId;
    protected $paymentId;
    protected $orderStatus;
    protected $amount;

    /**
     * Create a new message instance.
     *
     * @param  int|string  $orderId
     * @param  string  $paymentId
     * @param  string  $orderStatus
     * @param  float  $amount
     * @return void
     */
    public function __construct($orderId, $paymentId, $orderStatus, $amount)
    {
        $this->orderId = $orderId;
        $this->paymentId = $paymentId;
        $this->orderStatus = $orderStatus;
        $this->amount = $amount;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('cart.send_from'), config('cart.send_from_text'))
            ->subject('Статус оплаты заказа №' . $this->orderId)
            ->replyTo(config('cart.reply_to'), config('cart.reply_to_text'))
            ->view('mail.payment_status')
            ->with([
                'orderId' => $this->orderId,
                'paymentId' => $this->paymentId,
                'orderStatus' => $this->orderStatus,
                'amount' => $this->amount,
            ]);
    }
}
