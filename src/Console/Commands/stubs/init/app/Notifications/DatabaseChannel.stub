<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class DatabaseChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $notification->toBroadcast($notifiable);
    }
}
