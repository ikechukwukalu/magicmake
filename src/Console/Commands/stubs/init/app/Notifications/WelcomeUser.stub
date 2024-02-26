<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class WelcomeUser extends UserNotification
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(trans('notify.welcome.subject', ['name' => $this->user->name]))
                    ->line(trans('notify.welcome.introduction', ['name' => $this->user->name]))
                    ->line(trans('notify.welcome.message'))
                    ->action(trans('notify.welcome.action'), url(config('api.notification_url.registration.welcome_user')))
                    ->line(trans('notify.welcome.complimentary_close'));
    }
}
