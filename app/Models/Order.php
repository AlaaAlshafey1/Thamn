<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status', 'total_price', 'payload'];

    public function details()
    {
        return $this->hasMany(OrderDetails::class);
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
