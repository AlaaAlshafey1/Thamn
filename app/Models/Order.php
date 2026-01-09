<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id',"category_id", 'status', 'total_price', 'payload','ai_min_price','ai_max_price','ai_price','ai_confidence','ai_reasoning'];

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

}
