<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::whereNotNull('fcm_token')
    ->orWhereNotNull('fcm_token_android')
    ->orWhereNotNull('fcm_token_ios')
    ->first();

if ($user) {
    echo "Found user: " . $user->email . "\n";
    echo "fcm_token: " . $user->fcm_token . "\n";
    echo "fcm_token_android: " . $user->fcm_token_android . "\n";
    echo "fcm_token_ios: " . $user->fcm_token_ios . "\n";
} else {
    echo "No users with FCM token found in DB!\n";
    echo "\nAll users:\n";
    $all = App\Models\User::select('id','email','fcm_token','fcm_token_android','fcm_token_ios')->take(5)->get();
    foreach($all as $u) {
        echo "ID: {$u->id} | Email: {$u->email} | fcm: {$u->fcm_token} | android: {$u->fcm_token_android} | ios: {$u->fcm_token_ios}\n";
    }
}
