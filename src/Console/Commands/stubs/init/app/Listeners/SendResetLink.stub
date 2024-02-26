<?php

namespace App\Listeners;

use App\Events\ForgotPassword;
use Illuminate\Support\Facades\Password;

class SendResetLink extends UserEventListener
{
    private ForgotPassword $event;

    public function handle($event)
    {
        $this->event = $event;
        Password::sendResetLink(['email' => $this->event->user->email]);
    }
}
