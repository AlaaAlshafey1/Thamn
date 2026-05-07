<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\WhatsAppService;

$phone = '201021443985';
$otp = rand(1000, 9999);

echo "Attempting to send OTP ($otp) to: $phone ...\n";

$whatsapp = app(WhatsAppService::class);
$message = "كود تفعيل حسابك في ثمن هو: $otp . لا تشاركه مع أحد يا غالي.";

$result = $whatsapp->sendMessage($phone, $message);

if ($result) {
    echo "SUCCESS: WhatsApp sent.\n";
} else {
    echo "FAILED: WhatsApp not sent.\n";
}
