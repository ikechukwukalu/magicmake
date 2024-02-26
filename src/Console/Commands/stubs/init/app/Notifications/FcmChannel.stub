<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class FcmChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $notification->toFcm($notifiable);
    }
}
