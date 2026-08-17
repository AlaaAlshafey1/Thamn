<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ThamnEvaluationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunAiEvaluationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** عدد محاولات إعادة التشغيل عند الفشل */
    public int $tries = 2;

    /** مهلة الـ Job بالثواني — أطول بكثير من timeout الـ nginx */
    public int $timeout = 300;

    public function __construct(protected Order $order)
    {
    }

    public function handle(ThamnEvaluationService $evaluationService): void
    {
        Log::info("RunAiEvaluationJob: Starting for order #{$this->order->id}");

        try {
            $evaluationService->runAiEvaluation($this->order);

            if (!$this->order->evaluated_at) {
                $this->order->update(['evaluated_at' => now()]);
            }

            Log::info("RunAiEvaluationJob: Completed for order #{$this->order->id}");

        } catch (\Throwable $e) {
            Log::error("RunAiEvaluationJob: Failed for order #{$this->order->id}", [
                'error' => $e->getMessage(),
            ]);

            throw $e; // إعادة الرمي لتفعيل آلية إعادة المحاولة
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("RunAiEvaluationJob: All retries exhausted for order #{$this->order->id}", [
            'error' => $e->getMessage(),
        ]);
    }
}
