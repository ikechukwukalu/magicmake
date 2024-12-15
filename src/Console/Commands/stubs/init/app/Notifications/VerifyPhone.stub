<?php

namespace App\Notifications;

use App\Exceptions\SmsException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class VerifyPhone extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private string $otp, private array $user)
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

    public function toSms(object $notifiable): void
    {
        tryCatch(function () use($notifiable) {
            $url = env('YOUR_NOTIFY_URL') . '/campaigns/sms';

            $body = [
                    "name" => $this->user['name'],
                    "from" => env('APP_NAME'),
                    "text" => trans("verify.enter_otp", [
                        'code' => $this->otp,
                        'name' => $this->user['name']
                    ]),
                    "status" => "running",
                    "lists" => [
                      [
                        "name" => $notifiable->name,
                        "telephone" => config("countryByCode.{$notifiable->country}.phone.0") . $notifiable->phone
                      ]
                    ],
                    "schedule" => null,
                    "channel" => "sms",
                    "campaign_type" => "transactional"
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

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
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
