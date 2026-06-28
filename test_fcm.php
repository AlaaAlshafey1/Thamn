<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class FCMTest {
    use \App\Http\Traits\FCMOperation;
    public function test() {
        return $this->notifyByFirebase('Test Title', 'Test Body', ['fake_token_123'], ['data' => ['user_id' => 1]]);
    }
}

$test = new FCMTest();
print_r($test->test());
