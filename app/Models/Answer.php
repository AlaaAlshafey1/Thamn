<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'user_id',
        'question_id',
        'option_id',
        'sub_option_id',
        'value',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class, 'option_id');
    }

    public function subOption()
    {
        return $this->belongsTo(QuestionOption::class, 'sub_option_id');
    }
}


