<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PriceChangeNotification extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $product, public $oldPrice, public $newPrice)
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Product Price Change Notification')
            ->view('emails.price-change');
    }
}
