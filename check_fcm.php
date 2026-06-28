<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class FcmTestRunner {
    use App\Http\Traits\FCMOperation;
}

echo "Scanning database for users with active FCM tokens...\n";

$users = App\Models\User::all();
$allTokens = [];
$targetedUsers = [];

foreach ($users as $user) {
    $tokens = $user->getFcmTokens();
    if (!empty($tokens)) {
        foreach ($tokens as $token) {
            $allTokens[] = $token;
        }
        $targetedUsers[] = "ID: {$user->id} | Name: {$user->first_name} {$user->last_name} | Email: {$user->email} | Tokens: " . count($tokens);
    }
}

echo "Total targeted users found: " . count($targetedUsers) . "\n";
foreach ($targetedUsers as $info) {
    echo " - " . $info . "\n";
}
echo "Total unique FCM tokens to send: " . count($allTokens) . "\n";

if (empty($allTokens)) {
    echo "\nError: No active or valid FCM tokens found in the database. Sending cancelled.\n";
    exit(1);
}

echo "\nSending general test push notification to all devices...\n";
$runner = new FcmTestRunner();
$result = $runner->notifyByFirebase(
    "تنبيه عام من تطبيق ثمن! 📣",
    "يا هلا بالغالين! هذي رسالة تنبيه تجريبية عامة مرسلة لجميع مستخدمي التطبيق للتأكد من وصول الإشعارات.",
    $allTokens,
    ['data' => ['type' => 'general_announcement']]
);

echo "\nFirebase Response:\n";
print_r($result);
