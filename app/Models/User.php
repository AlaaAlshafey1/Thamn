<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles ,HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'image',
        'is_active',
        'social_id',
        'social_provider',
        'is_verified',
        'fcm_token_android',
        'fcm_token_ios',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
