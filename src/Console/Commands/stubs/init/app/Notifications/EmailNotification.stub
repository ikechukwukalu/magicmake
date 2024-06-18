<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class EmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private object $emailData)
    { }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(array|User $notifiable): MailMessage
    {
        $mail = (new MailMessage);
        $mail->subject($this->emailData->subject);
        // $mail->from($this->emailData->from);

        foreach($this->emailData->lines as $line) {
            $mail->line($line);
        }

        if ($this->emailData->action) {
            $mail->action($this->emailData->action_text, $this->emailData->action_url);
        }

        if ($this->emailData->attachements) {
            $fileUploads = $this->emailData->attachements;

            $i = 1;

            foreach ($fileUploads as $fileUpload) {
                $file = Storage::disk($fileUpload->disk)->get($fileUpload->relative_path);

                $mail->attachData($file, $fileUpload->name, [
                    'as' => $fileUpload->name,
                    'mime' => $fileUpload->mime_type,
                ]);

                $i ++;
            }
        }

        if ($this->emailData->remark) {
            $mail->line($this->emailData->remark);
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return (array) $this->emailData;
    }
}
