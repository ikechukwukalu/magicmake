<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class UserLogin extends UserNotification
{
    private string $time;
    private string $deviceAndLocation;
    private null|string $url;

    public function __construct(string $time, array $deviceAndLocation, null|string $url = null)
    {
        $this->time = $time;
        $this->deviceAndLocation = implode(", ", $deviceAndLocation);
        $this->url = $url;

        if (!$this->url) {
            $this->url = config('api.notification_url.login.user_login');
        }
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('notify.login.subject'))
            ->line(trans('notify.login.introduction', ['time' => $this->time, 'deviceAndLocation' => $this->deviceAndLocation]))
            ->line(trans('notify.login.message'))
            ->action(trans('notify.login.action'), $this->url)
            ->line(trans('notify.login.complimentary_close'));
    }
}
