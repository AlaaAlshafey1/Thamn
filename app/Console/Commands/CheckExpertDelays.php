<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use App\Notifications\ExpertEvaluationWarningNotification;
use App\Notifications\ExpertEvaluationWithdrawnNotification;
use App\Mail\SystemNotificationMail;
use App\Http\Traits\FCMOperation;

use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckExpertDelays extends Command
{
    use FCMOperation;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check-expert-delays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send warning to experts after 1 hour, withdraw order after 2 hours of no evaluation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $oneHourAgo = $now->copy()->subHour();
        $twoHoursAgo = $now->copy()->subHours(2);

        $warningCount = 0;
        $withdrawnCount = 0;

        // ──────────────────────────────────────────────────────────
        // 1. سحب الطلبات المتأخرة أكثر من ساعتين (يتم أولاً حتى لا نرسل إنذاراً لطلب سيُسحب)
        // ──────────────────────────────────────────────────────────
        $ordersToWithdraw = Order::where('status', 'beingEstimated')
            ->whereNotNull('expert_id')
            ->where(function ($q) {
                $q->where('expert_evaluated', 0)->orWhereNull('expert_evaluated');
            })
            ->where('accepted_at', '<=', $twoHoursAgo)
            ->get();

        foreach ($ordersToWithdraw as $order) {
            $expert = User::find($order->expert_id);

            // سحب الطلب من الخبير وإعادته للمنافسة
            $order->update([
                'expert_id' => null,
                'accepted_at' => null,
                'expert_warning_sent' => false,
                'status' => 'orderReceived', // يرجع متاح للخبراء
            ]);

            // إشعار الخبير بسحب الطلب
            if ($expert) {
                $expert->notify(new ExpertEvaluationWithdrawnNotification($order));

                // WhatsApp للخبير
                try {
                    if ($expert->phone) {
                        $whatsapp = app(\App\Services\WhatsAppService::class);
                        $msg = \App\Services\WhatsAppService::getTemplate('expert_order_withdrawn', ['id' => $order->id]);
                        $whatsapp->sendMessage($expert->phone, $msg);
                    }
                } catch (\Throwable $e) {
                    \Log::error('Expert Withdrawal WhatsApp Failed: ' . $e->getMessage());
                }


                // إيميل للخبير
                try {
                    if ($expert->email) {
                        Mail::to($expert->email)->send(new SystemNotificationMail(
                            'تم سحب الطلب منك بسبب التأخر في التقييم',
                            "نأسف لإبلاغك بأنه تم سحب الطلب رقم {$order->id} منك وذلك بسبب تأخرك في إتمام التقييم خلال المدة المحددة. هذا الإجراء يأتي ضمن سياسة التطبيق لضمان سرعة الخدمة وجودتها. نقدر جهودك ونتطلع لتعاونك في الطلبات القادمة بشكل أسرع. 💪",
                            route('orders.index')
                        ));
                    }
                } catch (\Throwable $e) {
                    \Log::error('Expert Withdrawal Email Failed: ' . $e->getMessage());
                }
            }

            // إعلام باقي خبراء نفس القسم بتوفر الطلب مجدداً
            try {
                $categoryExperts = User::role('expert')
                    ->where('category_id', $order->category_id)
                    ->when($expert, fn($q) => $q->where('id', '!=', $expert->id))
                    ->get();

                $whatsapp = app(\App\Services\WhatsAppService::class);
                foreach ($categoryExperts as $otherExpert) {
                    // WhatsApp
                    try {
                        if ($otherExpert->phone) {
                            $msg = \App\Services\WhatsAppService::getTemplate('new_order_expert', [
                                'name' => $otherExpert->first_name ?? '',
                                'category' => $order->category->name_ar ?? 'القسم',
                            ]);
                            $whatsapp->sendMessage($otherExpert->phone, $msg);
                        }
                    } catch (\Throwable $e) {
                        \Log::error('Expert Re-notify WhatsApp Failed: ' . $e->getMessage());
                    }


                }
            } catch (\Throwable $e) {
                \Log::error('Re-notify Category Experts Failed: ' . $e->getMessage());
            }

            $withdrawnCount++;
        }

        // ──────────────────────────────────────────────────────────
        // 2. إنذار الخبراء المتأخرين (أكثر من ساعة ولم يتم إنذارهم بعد)
        // ──────────────────────────────────────────────────────────
        $ordersToWarn = Order::where('status', 'beingEstimated')
            ->whereNotNull('expert_id')
            ->where(function ($q) {
                $q->where('expert_evaluated', 0)->orWhereNull('expert_evaluated');
            })
            ->where('expert_warning_sent', false)
            ->where('accepted_at', '<=', $oneHourAgo)
            ->where('accepted_at', '>', $twoHoursAgo) // لم تتجاوز الساعتين (تلك تُسحب)
            ->get();

        foreach ($ordersToWarn as $order) {
            $expert = User::find($order->expert_id);
            if (!$expert) {
                continue;
            }

            // تحديث حالة الإنذار
            $order->update(['expert_warning_sent' => true]);

            // إشعار Database
            $expert->notify(new ExpertEvaluationWarningNotification($order));

            // WhatsApp للخبير
            try {
                if ($expert->phone) {
                    $whatsapp = app(\App\Services\WhatsAppService::class);
                    $msg = \App\Services\WhatsAppService::getTemplate('expert_evaluation_warning', ['id' => $order->id]);
                    $whatsapp->sendMessage($expert->phone, $msg);
                }
            } catch (\Throwable $e) {
                \Log::error('Expert Warning WhatsApp Failed: ' . $e->getMessage());
            }


            // إيميل للخبير
            try {
                if ($expert->email) {
                    Mail::to($expert->email)->send(new SystemNotificationMail(
                        'تنبيه: لم تقيّم الطلب بعد! ⏰',
                        "مرحباً، مر أكثر من ساعة على استلامك الطلب رقم {$order->id} ولم يتم التقييم بعد. نرجو منك إتمام التقييم في أسرع وقت ممكن حتى لا يتم سحب الطلب منك. شاكرين تعاونك 🙏",
                        route('orders.show', $order->id)
                    ));
                }
            } catch (\Throwable $e) {
                \Log::error('Expert Warning Email Failed: ' . $e->getMessage());
            }

            $warningCount++;
        }

        // ──────────────────────────────────────────────────────────
        // 3. إشعارات تطمينية للعميل كل ~30 دقيقة أثناء فترة التقييم
        // ──────────────────────────────────────────────────────────
        $customerNotifyCount = 0;

        // رسائل تطمينية متنوعة للعميل
        $customerMessages = [
            [
                'title' => 'خبيرنا شغال على طلبك 🎯',
                'body'  => "يا هلا! خبيرنا المختص بدأ يشتغل على طلبك رقم #{ORDER_ID} ويدقق على كل التفاصيل. شوي وتوصلك النتيجة إن شاء الله.",
            ],
            [
                'title' => 'لا تشيل هم، التقييم ماشي تمام 💪',
                'body'  => "طلبك رقم #{ORDER_ID} تحت التقييم الحين من خبيرنا. يبي يعطيك أدق نتيجة عشان كذا ياخذ وقته. ترقب البشارة قريب!",
            ],
            [
                'title' => 'باقي شوي وتوصلك النتيجة 🔍',
                'body'  => "خبيرنا يدقق على كل التفاصيل في طلبك رقم #{ORDER_ID} عشان يعطيك تقييم دقيق ومضبوط. النتيجة قربت توصلك!",
            ],
            [
                'title' => 'طلبك في آخر المراحل ✨',
                'body'  => "تقييم طلبك رقم #{ORDER_ID} في آخر مراحله. جهّز نفسك للنتيجة يا بطل! بنبشرك قريب جداً إن شاء الله 🚀",
            ],
        ];

        $ordersInProgress = Order::where('status', 'beingEstimated')
            ->whereNotNull('expert_id')
            ->where(function ($q) {
                $q->where('expert_evaluated', 0)->orWhereNull('expert_evaluated');
            })
            ->whereNotNull('accepted_at')
            ->where('accepted_at', '<=', $now->copy()->subMinutes(30)) // مر على الأقل 30 دقيقة
            ->where('accepted_at', '>', $twoHoursAgo) // لم تتجاوز الساعتين
            ->get();

        foreach ($ordersInProgress as $order) {
            // حساب المرحلة الحالية بناءً على الوقت المنقضي (كل 30 دقيقة = مرحلة)
            $minutesElapsed = $now->diffInMinutes($order->accepted_at);
            $currentPhase = min(intval(floor($minutesElapsed / 30)), 4); // من 1 إلى 4

            // لو العميل استلم إشعار هذه المرحلة بالفعل، نتخطاه
            if ($order->customer_progress_notified >= $currentPhase) {
                continue;
            }

            // اختيار الرسالة المناسبة للمرحلة
            $messageIndex = min($currentPhase - 1, count($customerMessages) - 1);
            $message = $customerMessages[$messageIndex];

            $title = $message['title'];
            $body = str_replace('#{ORDER_ID}', $order->id, $message['body']);

            // إرسال FCM للعميل
            try {
                $tokens = $order->user->getFcmTokens();
                if (!empty($tokens)) {
                    $this->notifyByFirebase(
                        $title,
                        $body,
                        $tokens,
                        ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'customer_evaluation_progress']]
                    );
                }
            } catch (\Throwable $e) {
                \Log::error('Customer Progress FCM Failed: ' . $e->getMessage());
            }

            // تحديث عداد الإشعارات
            $order->update(['customer_progress_notified' => $currentPhase]);
            $customerNotifyCount++;
        }

        $this->info("✅ Warnings sent: {$warningCount} | Orders withdrawn: {$withdrawnCount} | Customer progress: {$customerNotifyCount}");
    }
}
