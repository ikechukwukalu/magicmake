<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class PasswordChange extends UserNotification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('notify.password.subject'))
            ->line(trans('notify.password.introduction'))
            ->line(trans('notify.password.message'))
            ->action(trans('notify.password.action'), route(config('api.notification_url.password.password_change')))
            ->line(trans('notify.password.complimentary_close'));
    }
}
