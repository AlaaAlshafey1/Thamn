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
        'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
