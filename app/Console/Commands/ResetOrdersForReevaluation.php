<?php

namespace App\Console\Commands;

use App\Jobs\RunAiEvaluationJob;
use App\Models\Order;
use Illuminate\Console\Command;

class ResetOrdersForReevaluation extends Command
{
    protected $signature = 'orders:reset-evaluation
        {ids* : Order IDs to reset}
        {--no-ai : Only reset the data, do not re-run the AI evaluation}
        {--force : Skip the confirmation prompt}';

    protected $description = 'Reset orders as if they were never evaluated (clears AI + expert + thamn results) and re-run the AI evaluation';

    public function handle(): int
    {
        $ids = array_map('intval', $this->argument('ids'));
        $orders = Order::whereIn('id', $ids)->get();

        $missing = array_diff($ids, $orders->pluck('id')->all());
        if ($missing) {
            $this->warn('Not found: ' . implode(', ', $missing));
        }

        if ($orders->isEmpty()) {
            return self::FAILURE;
        }

        $this->table(
            ['id', 'status', 'ai_price', 'expert_price', 'expert_id'],
            $orders->map(fn ($o) => [$o->id, $o->status, $o->ai_price, $o->expert_price, $o->expert_id])->all()
        );

        if (!$this->option('force') && !$this->confirm('Reset these ' . $orders->count() . ' orders and wipe their evaluation data?')) {
            return self::SUCCESS;
        }

        foreach ($orders as $order) {
            $order->update([
                'status'                     => 'beingEstimated',
                'ai_min_price'               => null,
                'ai_max_price'               => null,
                'ai_price'                   => null,
                'ai_confidence'              => null,
                'ai_reasoning'               => null,
                'ai_features'                => null,
                'ai_valuation_rating_id'     => null,
                'expert_id'                  => null,
                'expert_evaluated'           => 0,
                'expert_warning_sent'        => 0,
                'customer_progress_notified' => 0,
                'expert_price'               => null,
                'expert_min_price'           => null,
                'expert_max_price'           => null,
                'expert_reasoning'           => null,
                'thamn_price'                => null,
                'thamn_min_price'            => null,
                'thamn_max_price'            => null,
                'thamn_reasoning'            => null,
                'thamn_by'                   => null,
                'thamn_at'                   => null,
                'accepted_at'                => null,
                'evaluated_at'               => null,
                're_evaluation_count'        => 0,
            ]);

            if (!$this->option('no-ai')) {
                RunAiEvaluationJob::dispatch($order->fresh());
            }

            $this->info("Order #{$order->id} reset" . ($this->option('no-ai') ? '' : ' and AI evaluation queued'));
        }

        return self::SUCCESS;
    }
}
