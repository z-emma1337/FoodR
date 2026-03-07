<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
            ]
    );

    return (new MailMessage)
        ->greeting('Szia!')
        ->subject('Hitelesítsd a FoodR fiókodat!')
        ->line('Nyomj a gombra az e-mail címed megerősítéséhez.')
        ->action('E-mail cím megerősítése', $verificationUrl)
        ->line('Ha nem te regisztráltál, akkor nincs további teendőd.')
        ->salutation('Üdvözlettel, FoodR');
    }
}