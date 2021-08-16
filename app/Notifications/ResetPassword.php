<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
//use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\ResetPassword as Notification;

class ResetPassword extends Notification
{

    public function toMail($notifiable)
    {
        $url=config('app.client_url').'/password/reset/'.$this->token.
            '?email='.urlencode($notifiable->email);
        return (new MailMessage)
                    ->line('You receiving this email because we receive a password reset pass for your account.')
                    ->action('Reset Password', $url)
                    ->line('if you did not request a password reset,please resend request for change password');
    }


}
