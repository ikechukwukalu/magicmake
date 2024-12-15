<?php

namespace App\Events;

use App\Models\User;
use App\Events\UserEvent;

class PhoneVerification extends UserEvent
{

    public User $user;
    public string $otp;

    public function __construct(User $user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

}
