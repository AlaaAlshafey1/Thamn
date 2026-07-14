<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Notifications\OrderExpiredNotification;
use App\Mail\SystemNotificationMail;
use App\Http\Traits\FCMOperation;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckExpiredOrders extends Command
{
    use FCMOperation;
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

        // Notify user (Database)
        $order->user->notify(new OrderExpiredNotification($order));

        // FCM Notification
        $fcmToken = $order->user->fcm_token ?? $order->user->fcm_token_android ?? $order->user->fcm_token_ios;
        $userLang = $order->user->preferredLang();
        if ($fcmToken) {
            $this->notifyByFirebase(
                $userLang === 'ar' ? '⚠️ انتهت مهلة تقييم منتجك' : '⚠️ Evaluation Time Expired',
                $userLang === 'ar'
                    ? "نعتذر، لم يتم قبول طلبك رقم #{$order->id} من أي خبير في الوقت المحدد (24 ساعة). يمكنك طلب استرداد المبلغ."
                    : "We're sorry, your order #{$order->id} was not accepted by any expert within the allowed time (24 hours). You can request a refund.",
                [$fcmToken],
                ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_expired']]
            );
        }

        // Email
        try {
            Mail::to($order->user->email)->send(new SystemNotificationMail(
                $userLang === 'ar' ? 'لم يتم قبول طلبك من قبل الخبراء' : 'Your order was not accepted by experts',
                $userLang === 'ar'
                    ? "نعتذر منك، طلبك رقم {$order->id} لم يتم قبوله من قبل أي خبير في الوقت المحدد (24 ساعة). يمكنك الآن طلب استرداد المبلغ."
                    : "We're sorry, your order #{$order->id} was not accepted by any expert within 24 hours. You may now request a refund.",
                route('orders.show', $order->id)
            ));
        } catch (\Exception $e) {
            \Log::error('Mail failed for expired order: ' . $e->getMessage());
        }
    }
}
