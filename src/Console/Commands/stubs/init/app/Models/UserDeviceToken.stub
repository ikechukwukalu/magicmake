<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDeviceToken extends Model
{
    use HasFactory, SoftDeletes;
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'device_token',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

}
