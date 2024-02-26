<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Trashed
{

    public function scopeTrashed(Builder $query): void
    {
        $query->whereNotNull('deleted_at');
    }
}
