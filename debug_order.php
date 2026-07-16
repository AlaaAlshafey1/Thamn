<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Show orders where AI ran but total_price is wrong (0 or = payment amount)
$orders = \App\Models\Order::whereNotNull('ai_price')->latest()->take(5)->get();
echo "=== Latest 5 orders with AI price ===\n";
foreach ($orders as $o) {
    echo "ID={$o->id} type={$o->evaluation_type} status={$o->status} ai={$o->ai_price} total={$o->total_price}\n";
}
echo "\n";

// Check the specific order the user is testing (latest beingEstimated)
$o = \App\Models\Order::where('status', 'beingEstimated')->latest()->first()
   ?? \App\Models\Order::latest()->first();
echo "Target order ID : " . $o->id . "\n";
echo "evaluation_type : " . $o->evaluation_type . "\n";
echo "status          : " . $o->status . "\n";
echo "ai_price        : " . $o->ai_price . "\n";
echo "ai_min_price    : " . $o->ai_min_price . "\n";
echo "ai_max_price    : " . $o->ai_max_price . "\n";
echo "ai_reasoning    : " . mb_substr($o->ai_reasoning ?? '', 0, 100) . "\n";
echo "expert_price    : " . $o->expert_price . "\n";
echo "thamn_price     : " . $o->thamn_price . "\n";
echo "total_price     : " . $o->total_price . "\n";
