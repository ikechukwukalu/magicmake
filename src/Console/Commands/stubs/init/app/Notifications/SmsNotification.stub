<?php

namespace App\Notifications;

use App\Exceptions\SmsException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class SmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private object $smsData)
    { }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [SmsChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return (array) $this->smsData;
    }

    public function toSms(object $notifiable): void
    {
        tryCatch(function () use($notifiable) {
            $url = env('YOUR_NOTIFY_URL') . '/campaigns/sms';

            $body = [
                    "name" => $this->smsData->name,
                    "from" => $this->smsData->from,
                    "text" => $this->smsData->text,
                    "status" => "running",
                    "lists" => [
                      [
                        "name" => $notifiable->name,
                        "telephone" => config("countryByCode.{$notifiable->country}.phone.0") . $notifiable->phone
                      ]
                    ],
                    "schedule" => $this->smsData->schedule,
                    "channel" => "sms",
                    "campaign_type" => $this->smsData->campaign_type
            ];

            $response = Http::withHeaders($this->getHeaders())->post($url, $body);

            $message = trans('general.fail');
            if ($response->ok()) {
                $message = trans('general.success');
            }

            return responseData($response->successful(), $response->status(), $message, $response->json());
        },
        function(\Throwable $th) {
            throw new SmsException($th->getMessage(), $th->getCode(), $th);
        });
    }

    private function getHeaders(): array
    {
        $token = env("YOUR_NOTIFY_API_KEY");

        return [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ];
    }

}
