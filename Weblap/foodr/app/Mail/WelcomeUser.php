<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class WelcomeUser extends Mailable
{
    public function build()
    {
        return $this->subject('Welcome to Foodr')
                    ->view('emails.welcome');
    }
}