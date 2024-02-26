<?php

namespace App\Traits;

use App\Models\User;

trait ActivityTrait
{

    /**
     * Trait for creating activity log.
     *
     * @param User $user
     * @param mixed $performedOn
     * @param string $event
     * @param string $logName
     * @param string $description
     * @return void
     */
    public function setActivity(User $user, mixed $performedOn, string $event, string $logName, string $description): void{
        activity()
            ->causedBy($user)
            ->performedOn($performedOn)
            ->withProperties(['ip_address' => request()?->ip(),
                'caused_by' => $user->toJson(),
                'performed_on' => $performedOn->toJson(),
            ])
            ->useLog($logName)
            ->event($event)
            ->log($description);
    }
}
