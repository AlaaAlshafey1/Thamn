<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$instance = config('services.ultramsg.instance');
$token = config('services.ultramsg.token');
$baseUrl = "https://api.ultramsg.com/{$instance}";

echo "Instance: $instance\n";
echo "Token: $token\n";
echo "Base URL: $baseUrl\n";

try {
    $response = Http::withoutVerifying()->get("$baseUrl/instance/status", [
        'token' => $token
    ]);
    
    echo "Status Code: " . $response->status() . "\n";
    echo "Body: " . $response->body() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
