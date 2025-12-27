<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'question_id',
        'option_id',
        'sub_option_id',
        'value',
        'price',
        'status',
        "stageing"
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class);
    }

    public function steps()
    {
        return $this->belongsTo(QuestionStep::class);
    }

}
