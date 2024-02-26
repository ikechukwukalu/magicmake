<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class UserLogin extends UserNotification
{
    private string $time;
    private string $deviceAndLocation;

    public function __construct(string $time, array $deviceAndLocation)
    {
        $this->time = $time;
        $this->deviceAndLocation = implode(", ", $deviceAndLocation);
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('notify.login.subject'))
            ->line(trans('notify.login.introduction', ['time' => $this->time, 'deviceAndLocation' => $this->deviceAndLocation]))
            ->line(trans('notify.login.message'))
            ->action(trans('notify.login.action'), route(config('api.notification_url.login.user_login')))
            ->line(trans('notify.login.complimentary_close'));
    }
}
