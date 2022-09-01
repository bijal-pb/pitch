<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $business, $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($arguments)
    {
        $this->user = $arguments['user'];
        $this->business = $arguments['business'];
        $this->link = $arguments['link'];
        $this->subject = "Request for join team";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.teamRequest');
    }
}
