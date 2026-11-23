<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use App\Notifications\ExpertEvaluationWarningNotification;
use App\Notifications\ExpertEvaluationWithdrawnNotification;
use App\Mail\SystemNotificationMail;
use App\Http\Traits\FCMOperation;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
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
    protected $description = 'Send 30-min WhatsApp reminders to expert and customer, and handle delayed orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $maxWithdrawHours = 3; // سحب الطلب بعد 3 ساعات في حال عدم التقييم
        $maxWithdrawTime = $now->copy()->subHours($maxWithdrawHours);

        $expertRemindersCount = 0;
        $customerReassureCount = 0;
        $withdrawnCount = 0;

        $whatsapp = app(WhatsAppService::class);

        // ──────────────────────────────────────────────────────────
        // 1. سحب الطلبات المتأخرة أكثر من الحد الأقصى وإعادتها للمنافسة
        // ──────────────────────────────────────────────────────────
        $ordersToWithdraw = Order::where('status', 'beingEstimated')
            ->whereNotNull('expert_id')
            ->where(function ($q) {
                $q->where('expert_evaluated', 0)->orWhereNull('expert_evaluated');
            })
            ->whereNotNull('accepted_at')
            ->where('accepted_at', '<=', $maxWithdrawTime)
            ->get();

        foreach ($ordersToWithdraw as $order) {
            $expert = User::find($order->expert_id);

            // حفظ بيانات الخبير المسحوب منه الطلب في السجل للرجوع إليها وللأدمن
            $withdrawnList = is_array($order->withdrawn_experts) ? $order->withdrawn_experts : [];
            if ($expert) {
                $withdrawnList[] = [
                    'expert_id'         => $expert->id,
                    'name'              => trim(($expert->first_name ?? '') . ' ' . ($expert->last_name ?? '')) ?: ($expert->name ?? 'خبير #' . $expert->id),
                    'membership_number' => $expert->membership_number ?? ('#' . $expert->id),
                    'phone'             => $expert->phone ?? '-',
                    'city'              => $expert->city ?? '-',
                    'accepted_at'       => $order->accepted_at ? $order->accepted_at->toDateTimeString() : null,
                    'withdrawn_at'      => $now->toDateTimeString(),
                    'reason'            => "تأخر في إنجاز التقييم وتجاوز مهلة الـ {$maxWithdrawHours} ساعات دون رد",
                ];
            }

            // سحب الطلب من الخبير وإعادته للمنافسة وتصفير عدادات التذكيرات وحفظ السجل
            $order->update([
                'expert_id'                  => null,
                'accepted_at'                 => null,
                'expert_warning_sent'        => false,
                'expert_progress_notified'   => 0,
                'customer_progress_notified' => 0,
                'status'                     => 'orderReceived',
                'withdrawn_experts'          => $withdrawnList,
            ]);

            // إشعار الخبير بسحب الطلب عبر الواتساب والإيميل مع رجاء سرعة الرد في المرات القادمة
            if ($expert) {
                try {
                    $expert->notify(new ExpertEvaluationWithdrawnNotification($order));
                } catch (\Throwable $e) {
                    Log::error("Withdrawal Notification DB failed: " . $e->getMessage());
                }

                if ($expert->phone) {
                    try {
                        $msg = WhatsAppService::getTemplate('expert_order_withdrawn', ['id' => $order->id]);
                        $whatsapp->sendMessage($expert->phone, $msg);
                    } catch (\Throwable $e) {
                        Log::error('Expert Withdrawal WhatsApp Failed: ' . $e->getMessage());
                    }
                }

                if ($expert->email) {
                    try {
                        Mail::to($expert->email)->send(new SystemNotificationMail(
                            'سحب الطلب لتأخر التقييم',
                            "لقد تم سحب الطلب رقم : {$order->id} منك نظراً لتجاوز المهلة المحددة دون إنجاز التقييم. نرجو منك سرعة الرد وإنجاز الطلبات القادمة في وقتها حرصاً على استمرار عضويتك في منصة الخبراء.",
                            route('orders.index')
                        ));
                    } catch (\Throwable $e) {
                        Log::error('Expert Withdrawal Email Failed: ' . $e->getMessage());
                    }
                }
            }

            // إعلام باقي خبراء القسم بتوفر الطلب من جديد (باستثناء الخبير المسحوب منه والخبراء السابقين)
            try {
                $excludedExpertIds = collect($withdrawnList)->pluck('expert_id')->filter()->map(fn($id) => (int)$id)->all();
                if ($expert) {
                    $excludedExpertIds[] = (int) $expert->id;
                }
                $excludedExpertIds = array_values(array_unique($excludedExpertIds));

                $categoryExperts = User::role('expert')
                    ->where('category_id', $order->category_id)
                    ->whereNotIn('id', $excludedExpertIds)
                    ->get();

                foreach ($categoryExperts as $otherExpert) {
                    if ($otherExpert->phone) {
                        try {
                            $expertLink = URL::temporarySignedRoute(
                                'expert.auto-login',
                                now()->addDays(7),
                                ['user' => $otherExpert->id, 'redirect' => route('orders.show', $order->id)]
                            );
                            $categoryName = $order->category->name_ar ?? 'القسم';
                            $msg = WhatsAppService::getTemplate('order_reopened_for_experts', [
                                'name'     => $otherExpert->first_name ?? '',
                                'category' => $categoryName,
                                'id'       => $order->id,
                            ]);
                            $msg .= WhatsAppService::formatActionLink('تسجيل الدخول واستلام الطلب', $expertLink, '⚡');
                            $whatsapp->sendMessage($otherExpert->phone, $msg);
                        } catch (\Throwable $e) {
                            Log::error('Re-notify expert WhatsApp Failed: ' . $e->getMessage());
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Re-notify Category Experts Failed: ' . $e->getMessage());
            }

            $withdrawnCount++;
        }

        // ──────────────────────────────────────────────────────────
        // 2. تذكير الخبير وتطمين العميل كل 30 دقيقة أثناء فترة التقييم
        // ──────────────────────────────────────────────────────────
        $ordersInProgress = Order::where('status', 'beingEstimated')
            ->whereNotNull('expert_id')
            ->where(function ($q) {
                $q->where('expert_evaluated', 0)->orWhereNull('expert_evaluated');
            })
            ->whereNotNull('accepted_at')
            ->where('accepted_at', '<=', $now->copy()->subMinutes(30)) // مر 30 دقيقة على الأقل
            ->where('accepted_at', '>', $maxWithdrawTime)              // لم يتجاوز مهلة السحب
            ->with(['expert', 'user', 'category'])
            ->get();

        foreach ($ordersInProgress as $order) {
            $minutesElapsed = $now->diffInMinutes($order->accepted_at);
            $currentPhase = intval(floor($minutesElapsed / 30)); // 1 = 30m, 2 = 60m, 3 = 90m, 4 = 120m, 5 = 150m

            if ($currentPhase < 1) {
                continue;
            }

            $categoryName = $order->category ? ' بقسم [' . ($order->category->name_ar ?? $order->category->name_en) . ']' : '';

            // ── أ. رسائل الواتساب والتنبيهات للخبير (حث على سرعة الإنجاز) ──
            $expertNotified = (int) ($order->expert_progress_notified ?? 0);
            if ($expertNotified < $currentPhase && $order->expert) {
                $expert = $order->expert;

                $expertTemplate = match ($currentPhase) {
                    1 => 'expert_reminder_30m',
                    2 => 'expert_reminder_60m',
                    3 => 'expert_reminder_90m',
                    default => 'expert_reminder_repeat',
                };

                // إرسال واتساب للخبير مع رابط الدخول المباشر
                if ($expert->phone) {
                    try {
                        $expertLink = URL::temporarySignedRoute(
                            'expert.auto-login',
                            now()->addDays(2),
                            ['user' => $expert->id, 'redirect' => route('orders.show', $order->id)]
                        );
                        $expertMsg = WhatsAppService::getTemplate($expertTemplate, [
                            'id'       => $order->id,
                            'category' => $categoryName,
                        ]);
                        $expertMsg .= WhatsAppService::formatActionLink('إكمال تقييم الطلب الآن', $expertLink, '⚡');
                        $whatsapp->sendMessage($expert->phone, $expertMsg);
                    } catch (\Throwable $e) {
                        Log::error("Expert 30m Reminder WhatsApp Failed (Order {$order->id}): " . $e->getMessage());
                    }
                }

                // إشعار Database للخبير
                try {
                    $expert->notify(new ExpertEvaluationWarningNotification($order));
                } catch (\Throwable $e) {
                    Log::error("Expert Warning DB Notification Failed: " . $e->getMessage());
                }

                // إيميل للخبير في المراحل المتقدمة (ساعة وما بعد)
                if ($currentPhase >= 2 && $expert->email) {
                    try {
                        Mail::to($expert->email)->send(new SystemNotificationMail(
                            "تنبيه: سرعة إنجاز تقييم الطلب رقم {$order->id}",
                            "نود تذكيرك بأن الطلب رقم {$order->id} الذي قمت باستلامه بانتظار اعتمادك النهائي. يرجى الدخول وإنهاء التقييم لتفادي سحب الطلب.",
                            route('orders.show', $order->id)
                        ));
                    } catch (\Throwable $e) {
                        Log::error("Expert Warning Email Failed: " . $e->getMessage());
                    }
                }

                $order->update([
                    'expert_progress_notified' => $currentPhase,
                    'expert_warning_sent'      => true,
                ]);
                $expertRemindersCount++;
            }

            // ── ب. رسائل الواتساب والتطمين للعميل (الخبير مشغول بطلبات أخرى وأرجو تحلي الصبر) ──
            $customerNotified = (int) ($order->customer_progress_notified ?? 0);
            if ($customerNotified < $currentPhase && $order->user) {
                $customer = $order->user;
                $customerName = trim($customer->first_name . ' ' . $customer->last_name) ?: 'عميلنا العزيز';

                $customerTemplate = match ($currentPhase) {
                    1 => 'customer_reassurance_30m',
                    2 => 'customer_reassurance_60m',
                    3 => 'customer_reassurance_90m',
                    default => 'customer_reassurance_repeat',
                };

                // إرسال رسالة واتساب للعميل
                if ($customer->phone) {
                    try {
                        $customerWhatsappMsg = WhatsAppService::getTemplate($customerTemplate, [
                            'id'   => $order->id,
                            'name' => $customerName,
                        ]);
                        $whatsapp->sendMessage($customer->phone, $customerWhatsappMsg);
                    } catch (\Throwable $e) {
                        Log::error("Customer 30m Reassurance WhatsApp Failed (Order {$order->id}): " . $e->getMessage());
                    }
                }

                // إرسال إشعار FCM للتطبيق الجوال للعميل
                try {
                    $fcmTitles = [
                        1 => 'خبيرنا شغال على فحص طلبك 🔍',
                        2 => 'لا تشيل هم، التقييم ماشي تمام 💪',
                        3 => 'طلبك في مراحله النهائية ✨',
                        4 => 'جاري المراجعة والاعتماد النهائي 🚀',
                    ];
                    $fcmBodies = [
                        1 => "خبيرنا المعتمد يقوم حالياً بتدقيق تفاصيل طلبك رقم {$order->id} ولديه بعض الفحوصات الجارية، نشكر لك صبرك وقريباً تكتمل النتيجة.",
                        2 => "طلبك رقم {$order->id} قيد الفحص المتأني لضمان مطابقة المعايير وأسعار السوق العادلة، ترقب البشارة قريباً!",
                        3 => "طلبك رقم {$order->id} في اللمسات الأخيرة لدى الخبير المعتمد، ثواني أو دقائق وتكون النتيجة بين يديك.",
                        4 => "طلبك رقم {$order->id} في مرحلة الاعتماد النهائي وسنوافيك بالتقرير فور صدوره بإذن الله.",
                    ];

                    $title = $fcmTitles[$currentPhase] ?? 'طلبك قيد المتابعة والتدقيق 🔍';
                    $body = $fcmBodies[$currentPhase] ?? "طلبك رقم {$order->id} يخضع للمراجعة الدقيقة لدى الخبير المعتمد.";

                    $tokens = $customer->getFcmTokens();
                    if (!empty($tokens)) {
                        $this->notifyByFirebase(
                            $title,
                            $body,
                            $tokens,
                            ['data' => ['user_id' => $customer->id, 'order_id' => $order->id, 'type' => 'customer_evaluation_progress']]
                        );
                    }
                } catch (\Throwable $e) {
                    Log::error("Customer Progress FCM Failed (Order {$order->id}): " . $e->getMessage());
                }

                $order->update([
                    'customer_progress_notified' => $currentPhase,
                ]);
                $customerReassureCount++;
            }
        }

        $this->info("✅ Expert reminders: {$expertRemindersCount} | Customer reassurances: {$customerReassureCount} | Orders withdrawn: {$withdrawnCount}");
    }
}
