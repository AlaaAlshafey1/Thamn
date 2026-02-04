<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
        protected $fillable = [
        'phone',
        'email',
        'social_media',

    ];
        protected $casts = [
        'social_media' => 'array',
        ];
}
