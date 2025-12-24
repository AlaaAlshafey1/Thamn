<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionStep extends Model
{
    protected $table = 'question_steps';

    protected $fillable = [
        'name_ar',
        'name_en',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
