<?php

namespace App\Notifications;

use App\Events\UserNotificationEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DatabaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private object $userNotificationData)
    { }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', DatabaseChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toBroadcast(object $notifiable): void
    {
        UserNotificationEvent::dispatch($notifiable, $this->userNotificationData);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return (array) $this->userNotificationData;
    }
}
