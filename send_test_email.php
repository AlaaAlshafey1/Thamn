<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;

// Force Arabic locale so email renders in Arabic
app()->setLocale('ar');

Mail::to('alaa.alshafey12345@gmail.com')
    ->send(new OTPMail('35716', 'علاء'));

echo "✅ Email sent successfully!\n";
