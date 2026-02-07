<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'user_id',
        "category_id",
        'status',
        'total_price',
        'payload',
        'ai_min_price',
        'ai_max_price',
        'ai_price',
        'ai_confidence',
        'ai_reasoning',
        'expert_id',
        'expert_evaluated',
        'expert_price',
        'expert_min_price',
        'expert_max_price',
        'expert_reasoning',
        'thamn_price',
        'thamn_min_price',
        'thamn_max_price',
        'thamn_reasoning',
        'thamn_by',
        'thamn_at'
    ];

    protected $dates = ['deleted_at'];

    public function details()
    {
        return $this->hasMany(OrderDetails::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function files()
    {
        return $this->hasMany(OrderFiles::class);
    }
    public function payments()
    {
        return $this->hasMany(TapPayment::class);
    }

    public function expert()
    {
        return $this->belongsTo(User::class, 'expert_id');
    }

    // App\Models\Order.php

    public function calculateThamnPrice()
    {
        if (!$this->ai_price && !$this->expert_price) {
            return null;
        }

        $aiPrice = $this->ai_price ?? $this->expert_price;
        $expPrice = $this->expert_price ?? $this->ai_price;

        return round(($aiPrice + $expPrice) / 2, 2);
    }

    public function thamnUser()
    {
        return $this->belongsTo(User::class, 'thamn_by');
    }


}
