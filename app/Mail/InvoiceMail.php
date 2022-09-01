<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user, $transaction, $business, $pledge;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($arguments)
    {
        $this->user = $arguments['user'];
        $this->transaction = $arguments['transaction'];
        $this->business = $arguments['business'];
        $this->pledge = $arguments['pledge'];
        $this->subject = "Invoice";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.invoice');
    }
}
