<?php

namespace App\Notifications;

use App\Exceptions\FcmException;
use App\Models\UserDeviceToken;
use App\Notifications\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FcmNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private array $fcmData)
    { }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', FcmChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->fcmData;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toFcm($notifiable): void
    {
        $messaging = Firebase::messaging();
        $userDeviceTokens = UserDeviceToken::where('user_id', $notifiable->id)->get();

        try {
            foreach ($userDeviceTokens as $userDeviceToken) {
                $message = CloudMessage::fromArray([
                    'token' => $userDeviceToken->device_token,
                    'notification' => $this->fcmData,
                    'data' => []
                ]);

                $messaging->send($message);
            }
        } catch (\Throwable $th) {
            throw new FcmException($th->getMessage(), $th->getCode(), $th);
        }
    }

}
