<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class FcmTestRunner {
    use App\Http\Traits\FCMOperation;
}

// Find user dynamically by email or phone
$user = App\Models\User::where('email', 'alaa.alshafey12345@gmail.com')
    ->orWhere('phone', '+966555555553')
    ->orWhere('phone', '503921251005')
    ->first();

if (!$user) {
    echo "Error: User Alaa not found in database!\n";
    exit(1);
}

echo "Testing notification for user Alaa (ID: {$user->id})\n";
echo "Email: " . $user->email . "\n";
echo "Phone: " . $user->phone . "\n";

$tokens = $user->getFcmTokens();
echo "Valid filtered FCM tokens found: " . count($tokens) . "\n";
print_r($tokens);

if (empty($tokens)) {
    echo "Error: No valid filtered FCM tokens found. Sending cancelled.\n";
    exit(1);
}

echo "\nSending welcome push notification...\n";
$runner = new FcmTestRunner();
$result = $runner->notifyByFirebase(
    "أهلاً بك في ثمن! 🎉",
    "يا هلا بك يا أستاذ علاء، هذي رسالة ترحيبية تجريبية للتأكد من عمل التنبيهات بالشكل الصحيح.",
    $tokens,
    ['data' => ['type' => 'welcome_test', 'user_id' => $user->id]]
);

echo "\nFirebase Response:\n";
print_r($result);
