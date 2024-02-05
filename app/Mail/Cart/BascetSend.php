<?php

namespace App\Mail\Cart;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BascetSend extends Mailable
{
    use Queueable, SerializesModels;

    protected $formData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('cart.send_from'), config('cart.send_from_text'))
            ->subject(config('cart.subject'))
            ->replyTo(config('cart.reply_to'), config('cart.reply_to_text'))
            ->view('mail.bascetmail')
            ->with([
            "formData" => $this->formData
        ]);
    }
}
