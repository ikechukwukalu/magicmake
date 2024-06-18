<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class PasswordChange extends UserNotification
{

    private null|string $url;

    public function __construct(null|string $url = null)
    {
        $this->url = $url;

        if (!$this->url) {
            $this->url = config('api.notification_url.password.password_change');
        }
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('notify.password.subject'))
            ->line(trans('notify.password.introduction'))
            ->line(trans('notify.password.message'))
            ->action(trans('notify.password.action'), $this->url)
            ->line(trans('notify.password.complimentary_close'));
    }
}
