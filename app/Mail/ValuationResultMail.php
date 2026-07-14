<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ValuationResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $evaluationType;
    public ?float $minPrice;
    public ?float $maxPrice;
    public ?float $recommendedPrice;
    public ?string $reasoning;
    public string $categoryName;
    public bool $canReEvaluate;

    /**
     * @param Order  $order
     * @param string $evaluationType  ai|expert|thamn
     */
    public function __construct(Order $order, string $evaluationType = 'ai')
    {
        $this->order = $order;
        $this->evaluationType = $evaluationType;

        // Determine prices based on evaluation type
        switch ($evaluationType) {
            case 'expert':
                $this->minPrice = $order->expert_min_price ? (float) $order->expert_min_price : null;
                $this->maxPrice = $order->expert_max_price ? (float) $order->expert_max_price : null;
                $this->recommendedPrice = $order->expert_price ? (float) $order->expert_price : null;
                $this->reasoning = $order->expert_reasoning;
                break;

            case 'best':
            case 'thamn':
                $this->minPrice = $order->thamn_min_price ? (float) $order->thamn_min_price : null;
                $this->maxPrice = $order->thamn_max_price ? (float) $order->thamn_max_price : null;
                $this->recommendedPrice = $order->thamn_price ? (float) $order->thamn_price : null;
                $this->reasoning = $order->thamn_reasoning;
                break;

            case 'ai':
            default:
                $this->minPrice = $order->ai_min_price ? (float) $order->ai_min_price : null;
                $this->maxPrice = $order->ai_max_price ? (float) $order->ai_max_price : null;
                $this->recommendedPrice = $order->ai_price ? (float) $order->ai_price : null;
                $this->reasoning = $order->ai_reasoning;
                break;
        }

        $this->categoryName = $order->category?->name_ar ?? $order->category?->name_en ?? 'غير محدد';
        $this->canReEvaluate = ($order->re_evaluation_count ?? 0) < 1;
    }

    public function build()
    {
        return $this->subject('نتيجة تقييم طلبك رقم #' . $this->order->id . ' — تطبيق ثمن')
            ->view('emails.valuation_result');
    }
}
