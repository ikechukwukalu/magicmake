<?php

namespace App\Models;

use App\Models\Scopes\AdminScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends User
{
    use HasFactory;

    protected $table = 'users';

    protected static function booted(): void
    {
        static::addGlobalScope(new AdminScope);
    }

    protected function model(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Admin::class,
        );
    }

    public function url(): string
    {
        return env('APP_URL');
    }
}
