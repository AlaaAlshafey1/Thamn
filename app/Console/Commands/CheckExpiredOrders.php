<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Notifications\OrderExpiredNotification;
use App\Mail\SystemNotificationMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for orders not accepted or evaluated within 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $twentyFourHoursAgo = $now->copy()->subHours(24);

        // 1. Orders not accepted within 24 hours
        $unacceptedOrders = Order::whereIn('status', ['orderReceived', 'paid'])
            ->whereNull('expert_id')
            ->where('created_at', '<=', $twentyFourHoursAgo)
            ->get();

        foreach ($unacceptedOrders as $order) {
            $this->expireOrder($order);
        }

        // 2. Orders accepted but not evaluated within 24 hours
        $unevaluatedOrders = Order::where('status', 'beingEstimated')
            ->whereNotNull('expert_id')
            ->where('accepted_at', '<=', $twentyFourHoursAgo)
            ->get();

        foreach ($unevaluatedOrders as $order) {
            $this->expireOrder($order);
        }

        $this->info('Checked ' . ($unacceptedOrders->count() + $unevaluatedOrders->count()) . ' orders.');
    }

    protected function expireOrder($order)
    {
        $order->update(['status' => 'expired']);

        // Notify user
        $order->user->notify(new OrderExpiredNotification($order));

        // Email
        try {
            Mail::to($order->user->email)->send(new SystemNotificationMail(
                'لم يتم قبول طلبك من قبل الخبراء',
                "نعتذر منك، طلبك رقم {$order->id} لم يتم قبوله من قبل أي خبير في الوقت المحدد (24 ساعة). يمكنك الآن طلب استرداد المبلغ.",
                route('orders.show', $order->id)
            ));
        } catch (\Exception $e) {
            \Log::error('Mail failed for expired order: ' . $e->getMessage());
        }
    }
}
