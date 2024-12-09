<?php

namespace App\Traits;

trait DefaultOrderTrait
{

    /**
     * Does something.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public static function bootDefaultOrderTrait(): void
    {
        static::addGlobalScope(function ($query) {
            $query->orderBy('created_at', 'desc');
        });
    }
}
