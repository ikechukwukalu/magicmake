<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OldPassword extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'password',
    ];

    protected $hidden = [
        'password'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
