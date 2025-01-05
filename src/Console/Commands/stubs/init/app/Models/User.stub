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
        'otp',
        'model',
        'two_factor',
        'socialite_signup',
        'form_signup',
        'email_verified_at',
        'kyc_verified_at',
        'phone_verified_at',
        'suspended_at',
        'deactivated_at',
        'otp_expires_at',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'otp',
        'pin',
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
        'kyc_completed_at' => 'datetime',
        'suspended_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'active' => 'boolean',
        'two_factor' => 'boolean',
        'socialite_signup' => 'boolean',
        'form_signup' => 'boolean',
        'password' => 'hashed',
        'otp' => 'hashed',
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
