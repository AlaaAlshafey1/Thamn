<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFiles extends Model
{
    protected $fillable = [
        'order_id',
        'file_name',
        'file_path',
        'type'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }}
