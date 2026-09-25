<?php
/**
 * send_test_emails.php
 * =====================
 * Usage: php send_test_emails.php [email_name]
 *
 * Examples:
 *   php send_test_emails.php                  → sends ALL emails
 *   php send_test_emails.php otp              → sends OTP only
 *   php send_test_emails.php reset_password   → sends reset password only
 *
 * Available:
 *   otp | reset_password | welcome | expert_registration | valuation_result
 *   admin_withdrawal | arbitrator_declaration | declaration_signed
 *   expert_valuation | invoice | system_notification | expert_declaration
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

app()->setLocale('ar');

$TO = 'alaa.alshafey12345@gmail.com';

// ── Mock Data ──────────────────────────────────────────────
$fakeUser = new stdClass();
$fakeUser->first_name = 'علاء';
$fakeUser->last_name  = 'الشافعي';
$fakeUser->email      = $TO;

$fakeCategory = new stdClass();
$fakeCategory->name_ar = 'سيارات فاخرة';
$fakeCategory->name_en = 'Luxury Cars';

$fakeOrder = new stdClass();
$fakeOrder->id                 = 2456;
$fakeOrder->price              = 450;
$fakeOrder->created_at         = now();
$fakeOrder->category           = $fakeCategory;
$fakeOrder->ai_min_price       = 38000;
$fakeOrder->ai_max_price       = 46000;
$fakeOrder->ai_price           = 42000;
$fakeOrder->ai_reasoning       = 'السيارة بحالة ممتازة. الكيلومتراج منخفض نسبياً والمحرك بدون مشاكل. السوق الحالي يدعم هذا السعر.';
$fakeOrder->user               = $fakeUser;
$fakeOrder->re_evaluation_count = 0;

$fakeWithdrawal             = new stdClass();
$fakeWithdrawal->amount     = 1250.00;
$fakeWithdrawal->iban       = 'SA0380000000608010167519';
$fakeWithdrawal->created_at = now();
$fakeWithdrawal->user       = $fakeUser;

$fakeArbitrator            = new stdClass();
$fakeArbitrator->full_name = 'محمد عبدالله';

$fakeDeclaration            = new stdClass();
$fakeDeclaration->full_name = 'محمد عبدالله';
$fakeDeclaration->pdf_path  = null;

// ── Email definitions ──────────────────────────────────────
$emails = [

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
        'subject' => 'نتيجة تقييم طلبك رقم #2456 — ثمن',
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
        'subject' => 'وثيقة الشروط والأحكام وإقرار السرية - ثمن',
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
            'title'       => 'إشعار تجريبي',
            'messageBody' => 'هذا إشعار تجريبي من منصة ثمن.',
            'actionUrl'   => 'https://thmmn.net',
        ],
    ],

    'expert_declaration' => [
        'subject' => 'وثيقة إقرار الخبير - ثمن',
        'view'    => 'emails.expert_declaration',
        'data'    => ['arbitrator' => $fakeArbitrator, 'declarationUrl' => 'https://thmmn.net/sign'],
    ],
];

// ── Determine which to send ────────────────────────────────
$requested = $argv[1] ?? 'all';
$toSend = $requested === 'all' ? array_keys($emails) : [$requested];

echo "\n📧 إرسال الإيميلات إلى: $TO\n";
echo str_repeat('─', 50) . "\n\n";

foreach ($toSend as $key) {
    if (!isset($emails[$key])) {
        echo "⚠️  الإيميل '$key' غير موجود!\n";
        continue;
    }

    $cfg = $emails[$key];
    echo "⏳ إرسال: {$cfg['subject']} ...\n";

    try {
        Mail::send($cfg['view'], $cfg['data'], function ($m) use ($TO, $cfg) {
            $m->to($TO)->subject($cfg['subject']);
        });
        echo "✅ نجح: {$cfg['subject']}\n\n";
    } catch (\Exception $e) {
        echo "❌ فشل: " . $e->getMessage() . "\n\n";
    }

    sleep(1); // delay to avoid SMTP throttling
}

echo str_repeat('─', 50) . "\n";
echo "✔️  انتهى! تحقق من صندوق الوارد.\n\n";
