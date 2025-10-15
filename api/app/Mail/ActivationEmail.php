<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class ActivationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $activationLink;

    public function __construct($user)
    {
        $this->user = $user;
        $this->activationLink = url("/activate-account/{$user->activation_token}");
    }

    public function build()
    {
        return $this->subject('Activation de votre compte')
            ->view('emails.activation');
    }
}
