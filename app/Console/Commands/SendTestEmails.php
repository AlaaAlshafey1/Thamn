<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmails extends Command
{
    protected $signature   = 'email:test {name=all} {--to=alaa.alshafey12345@gmail.com}';
    protected $description = 'Send test emails for all or a specific template';

    public function handle(): int
    {
        app()->setLocale('ar');
        $to   = $this->option('to');
        $name = $this->argument('name');

        // ── Mock Data ──────────────────────────────────────────
        $fakeUser = new \stdClass();
        $fakeUser->first_name = 'علاء';
        $fakeUser->last_name  = 'الشافعي';
        $fakeUser->email      = $to;

        $fakeCategory         = new \stdClass();
        $fakeCategory->name_ar = 'سيارات فاخرة';
        $fakeCategory->name_en = 'Luxury Cars';

        $fakeOrder                    = new \stdClass();
        $fakeOrder->id                = 2456;
        $fakeOrder->price             = 450;
        $fakeOrder->created_at        = now();
        $fakeOrder->category          = $fakeCategory;
        $fakeOrder->ai_min_price      = 38000;
        $fakeOrder->ai_max_price      = 46000;
        $fakeOrder->ai_price          = 42000;
        $fakeOrder->ai_reasoning      = 'السيارة بحالة ممتازة، الكيلومتراج منخفض والمحرك سليم.';
        $fakeOrder->user              = $fakeUser;
        $fakeOrder->re_evaluation_count = 0;

        $fakeWithdrawal             = new \stdClass();
        $fakeWithdrawal->amount     = 1250.00;
        $fakeWithdrawal->iban       = 'SA0380000000608010167519';
        $fakeWithdrawal->created_at = now();
        $fakeWithdrawal->user       = $fakeUser;

        $fakeArbitrator            = new \stdClass();
        $fakeArbitrator->full_name = 'محمد عبدالله';

        $fakeDeclaration            = new \stdClass();
        $fakeDeclaration->full_name = 'محمد عبدالله';
        $fakeDeclaration->pdf_path  = null;

        // ── Templates ──────────────────────────────────────────
        $templates = [
            'otp' => [
                'subject' => 'كود تفعيل حساب ثمن: 35716',
                'view'    => 'emails.otp',
                'data'    => ['otp' => '35716', 'userName' => 'علاء'],
            ],
            'reset_password' => [
                'subject' => 'رمز إعادة تعيين كلمة المرور - ثمن',
                'view'    => 'emails.reset_password_otp',
                'data'    => ['otp' => '98341', 'userName' => 'علاء'],
            ],
            'welcome' => [
                'subject' => 'مرحباً بك في ثمن',
                'view'    => 'emails.welcome',
                'data'    => ['user' => $fakeUser, 'title' => 'مرحباً بك'],
            ],
            'expert_registration' => [
                'subject' => 'شكراً لتسجيلك كخبير في تطبيق ثمن',
                'view'    => 'emails.expert_registration',
                'data'    => ['user' => $fakeUser, 'password' => 'Secret@123'],
            ],
            'valuation_result' => [
                'subject' => 'نتيجة تقييم طلبك #2456 — ثمن',
                'view'    => 'emails.valuation_result',
                'data'    => [
                    'order'            => $fakeOrder,
                    'evaluationType'   => 'ai',
                    'minPrice'         => 38000,
                    'maxPrice'         => 46000,
                    'recommendedPrice' => 42000,
                    'reasoning'        => $fakeOrder->ai_reasoning,
                    'categoryName'     => 'سيارات فاخرة',
                    'canReEvaluate'    => true,
                ],
            ],
            'admin_withdrawal' => [
                'subject' => 'طلب سحب رصيد جديد من الخبير: علاء',
                'view'    => 'emails.admin_withdrawal',
                'data'    => ['withdrawal' => $fakeWithdrawal],
            ],
            'arbitrator_declaration' => [
                'subject' => 'وثيقة الشروط والأحكام - ثمن',
                'view'    => 'emails.arbitrator_declaration',
                'data'    => ['arbitrator' => $fakeArbitrator, 'declarationUrl' => 'https://thmmn.net/sign'],
            ],
            'declaration_signed' => [
                'subject' => 'نسختك من الاتفاقية القانونية - ثمن',
                'view'    => 'emails.declaration_signed',
                'data'    => ['declaration' => $fakeDeclaration, 'downloadUrl' => 'https://thmmn.net/download'],
            ],
            'expert_valuation' => [
                'subject' => 'تم تعيين طلب تقييم جديد لك - ثمن',
                'view'    => 'emails.expert_valuation',
                'data'    => ['order' => $fakeOrder, 'expert' => $fakeUser],
            ],
            'invoice' => [
                'subject' => 'فاتورة طلب رقم #2456 - ثمن',
                'view'    => 'emails.invoice',
                'data'    => ['order' => $fakeOrder],
            ],
            'system_notification' => [
                'subject' => 'إشعار تجريبي من ثمن',
                'view'    => 'emails.system_notification',
                'data'    => [
                    'title'       => 'إشعار تجريبي 🔔',
                    'messageBody' => 'هذا إشعار تجريبي من منصة ثمن للتأكد من أن كل شيء يعمل بشكل سليم.',
                    'actionUrl'   => 'https://thmmn.net',
                ],
            ],
            'expert_declaration' => [
                'subject' => 'وثيقة إقرار الخبير - ثمن',
                'view'    => 'emails.expert_declaration',
                'data'    => ['arbitrator' => $fakeArbitrator, 'declarationUrl' => 'https://thmmn.net/sign'],
            ],
        ];

        $toSend = ($name === 'all') ? array_keys($templates) : [$name];

        $this->info("\n📧 الإرسال إلى: {$to}\n" . str_repeat('─', 50));

        $success = 0;
        $fail    = 0;

        foreach ($toSend as $key) {
            if (!isset($templates[$key])) {
                $this->warn("⚠️  الإيميل '{$key}' غير موجود!");
                continue;
            }

            $cfg = $templates[$key];
            $this->line("⏳ {$cfg['subject']}");

            try {
                Mail::send($cfg['view'], $cfg['data'], function ($m) use ($to, $cfg) {
                    $m->to($to)->subject($cfg['subject']);
                });
                $this->info("   ✅ نجح!");
                $success++;
            } catch (\Exception $e) {
                $this->error("   ❌ فشل: " . $e->getMessage());
                $fail++;
            }

            sleep(1);
        }

        $this->line("\n" . str_repeat('─', 50));
        $this->info("✅ نجح: {$success}   ❌ فشل: {$fail}");

        return Command::SUCCESS;
    }
}
