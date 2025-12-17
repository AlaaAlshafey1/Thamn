<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'parent_option_id',
        'option_ar',
        'option_en',
        "description_ar",
        "description_en",
        'image',
        'order',
        'price',
        'badge',
        'sub_options_title',
        'min',
        'max',
        'is_active',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function subOptions()
    {
        return $this->hasMany(self::class, 'parent_option_id')
                    ->where('is_active', 1)
                    ->orderBy('order');
    }
}

