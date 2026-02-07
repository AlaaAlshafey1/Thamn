<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'image',
        'is_active',
        'bank_name',
        'iban',
        'account_number',
        'swift',
        'experience',
        'certificates',
        'notes',
        'balance',
        'social_id',
        'social_provider',
        'is_verified',
        'fcm_token_android',
        'fcm_token_ios',
        'fcm_token',
        'device_type',
        'role_id',
        'news_enabled',
        'email_enabled',
        'sms_enabled',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'news_enabled' => 'boolean',
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
    ];

    public function mainRole()
    {
        return $this->belongsTo(Role::class);
    }
    public function expertOrders()
    {
        return $this->hasMany(Order::class, 'expert_id');
    }


}
