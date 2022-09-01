<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeTeamMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user, $business;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($arguments)
    {
        $this->user = $arguments['user'];
        $this->business = $arguments['business'];
        $this->subject = "Welcome in Team";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.welcomeTeam');
    }
}
