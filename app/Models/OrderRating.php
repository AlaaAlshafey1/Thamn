<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRating extends Model
{
    use HasFactory;

    protected $table = 'order_ratings';

    protected $fillable = [
        'order_id',
        'user_id',
        'expert_id',
        'category_id',
        'rating',
        'feedback',
        'channel',
        'is_resolved',
        'admin_notes',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'rating'      => 'integer',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expert()
    {
        return $this->belongsTo(User::class, 'expert_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getEmojiAttribute(): string
    {
        return match ($this->rating) {
            1 => '😡',
            2 => '🙁',
            3 => '😐',
            4 => '🙂',
            5 => '😍',
            default => '⭐',
        };
    }

    public function getLabelAttribute(): string
    {
        return match ($this->rating) {
            1 => 'سيئة جداً',
            2 => 'سيئة',
            3 => 'متوسطة',
            4 => 'جيدة',
            5 => 'ممتازة',
            default => 'غير محدد',
        };
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->rating) {
            1 => 'badge bg-danger text-white',
            2 => 'badge bg-danger-transparent text-danger',
            3 => 'badge bg-warning text-dark',
            4 => 'badge bg-info text-white',
            5 => 'badge bg-success text-white',
            default => 'badge bg-secondary text-white',
        };
    }

    public function isDissatisfied(): bool
    {
        return $this->rating <= 2;
    }

    public function isSatisfied(): bool
    {
        return $this->rating >= 4;
    }
}
