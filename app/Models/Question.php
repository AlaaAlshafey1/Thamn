<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'question_ar',
        'question_en',
        'description_ar',
        'description_en',
        'type',
        'is_required',
        'is_active',
        'order',
        'min_value',
        'max_value',
        'step'
    ];

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id', 'id')
                    ->where('is_active', 1)
                    ->orderBy('order');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
    