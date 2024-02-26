<?php

namespace App\Models;

use App\Traits\MustVerifyPhone;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laragear\TwoFactor\TwoFactorAuthentication;
use Laragear\TwoFactor\Contracts\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements TwoFactorAuthenticatable, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthentication;
    use HasRoles, MustVerifyPhone;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'model',
        'two_factor',
        'socialite_signup',
        'form_signup',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'active' => 'boolean',
        'two_factor' => 'boolean',
        'socialite_signup' => 'boolean',
        'form_signup' => 'boolean',
        'password' => 'hashed',
    ];

    public function scopeCustomer(Builder $query): void
    {
        $query->where('model', Customer::class);
    }

    protected function getDefaultGuardName(): string
    {
        return 'web';
    }

}
