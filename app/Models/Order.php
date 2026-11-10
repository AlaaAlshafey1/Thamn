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
        'previous_price',
        'payload',
        'ai_min_price',
        'ai_max_price',
        'ai_price',
        'ai_confidence',
        'ai_reasoning',
        'expert_id',
        'expert_evaluated',
        'expert_warning_sent',
        'customer_progress_notified',
        'expert_price',
        'expert_min_price',
        'expert_max_price',
        'expert_reasoning',
        'thamn_price',
        'thamn_min_price',
        'thamn_max_price',
        'thamn_reasoning',
        'thamn_by',
        'thamn_at',
        'payment_type',
        'accepted_at',
        'evaluated_at',
        're_evaluation_count',
        'objection_reason',
        'objection_reason_id',
        'objected_at',
        'objection_admin_reply',
        'objection_replied_at',
        'ai_features',
        'evaluation_tracking',
        'is_ai_evaluated',
        'ai_confidence_score',
        'can_send_to_market',
        'create_image_ai',
        'ai_valuation_rating_id',
    ];

    protected $casts = [
        'create_image_ai' => 'boolean',
        'can_send_to_market' => 'boolean',
        'ai_features' => 'array',
        'evaluated_at' => 'datetime',
        'accepted_at' => 'datetime',
        'thamn_at' => 'datetime',
        'objected_at' => 'datetime',
        'objection_replied_at' => 'datetime',
    ];

    /**
     * Bootstrap the model and its traits.
     */
    protected static function booted()
    {
        static::updated(function (Order $order) {
            if ($order->wasChanged('status')) {
                try {
                    $order->sendOrderUpdatedFcm();
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Order status FCM data-only failed: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Send pure data-only FCM push to mobile device(s) when order status changes.
     */
    public function sendOrderUpdatedFcm(): array
    {
        $tokens = [];

        // 1. Customer tokens
        if ($this->user) {
            $tokens = array_merge($tokens, $this->user->getFcmTokens());
        }

        // 2. Assigned Expert tokens (if any)
        if ($this->expert) {
            $tokens = array_merge($tokens, $this->expert->getFcmTokens());
        }

        $tokens = array_values(array_unique(array_filter($tokens)));

        if (empty($tokens)) {
            return ['success' => false, 'error' => 'No FCM tokens found'];
        }

        return \App\Http\Traits\FCMOperation::sendDataOnly($tokens, [
            'type'     => 'order_updated',
            'order_id' => (string) $this->id,
            'refresh'  => '1',
        ]);
    }

    public function objectionReason()
    {
        return $this->belongsTo(ObjectionReason::class, 'objection_reason_id');
    }

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

    public function aiValuationRating()
    {
        return $this->belongsTo(AiValuationRating::class, 'ai_valuation_rating_id');
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

    public function getEvaluationTypeAttribute()
    {
        $rateTypeAnswer = $this->details()->whereHas('question', function ($q) { 
            $q->where('type', 'rateTypeSelection'); 
        })->first();
        return $rateTypeAnswer?->option?->badge ?? $rateTypeAnswer?->value ?? 'expert';
    }

    public function getAverageMarketPriceAttribute()
    {
        // Get the chosen evaluation type
        $rateTypeAnswer = $this->details()->whereHas('question', function ($q) { 
            $q->where('type', 'rateTypeSelection'); 
        })->first();
        $evaluationType = $rateTypeAnswer?->option?->badge ?? $rateTypeAnswer?->value;

        $min = 0;
        $max = 0;
        $fallback = 0;

        if ($evaluationType === 'ai') {
            return $this->ai_price ?? 0;
        } elseif ($evaluationType === 'expert') {
            return $this->expert_price ?? 0;
        } else {
            // thamn or default
            return $this->thamn_price ?? $this->expert_price ?? $this->ai_price ?? 0;
        }
    }

    public function thamnUser()
    {
        return $this->belongsTo(User::class, 'thamn_by');
    }

    public function refundRequest()
    {
        return $this->hasOne(RefundRequest::class);
    }
}
