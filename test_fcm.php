<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class FCMTest {
    use \App\Http\Traits\FCMOperation;
    public function test($token) {
        return $this->notifyByFirebase(
            'مرحباً من ثمن! 🔥',
            'وصلك إشعار Firebase بنجاح! النظام شغال تمام.',
            [$token],
            ['data' => ['user_id' => 1, 'type' => 'test_notification']]
        );
    }
}

$token = 'cmnSFPKVR0-RhoSDc8T4A0:APA91bFq55jMU0ieAnSfWrMQneBlsk9T30qNdRg7whE-e8r4by4pWmNZexeuC8MRWrhCxBPocsLVFbaYHBHJ8Ca9iggQB6LAN9Ph6PapgvaPzarwFK-hO_A';

$test = new FCMTest();
$result = $test->test($token);
print_r($result);
