<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $url;
    public string $userName;

    public function __construct(string $url, string $userName)
    {
        $this->url = $url;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Réinitialisation de votre mot de passe - CRM RH')
                    ->markdown('emails.auth.reset-password');
    }
}
