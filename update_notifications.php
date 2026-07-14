<?php
$dir = __DIR__ . '/app/Notifications';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);

    // 1. Update via() method
    if (preg_match('/public function via\(\$notifiable\)\s*\{\s*return\s+\[(.*?)\];/s', $content, $matches)) {
        $channels = trim($matches[1]);

        if (strpos($channels, 'WhatsAppChannel') === false) {
            $channelsArr = array_map('trim', explode(',', $channels));
            $channelsArr = array_filter($channelsArr);

            if (!in_array("'mail'", $channelsArr) && !in_array('"mail"', $channelsArr)) {
                $channelsArr[] = "'mail'";
            }
            $channelsArr[] = "\App\Channels\WhatsAppChannel::class";

            $newChannels = implode(', ', $channelsArr);
            $newVia = "public function via(\$notifiable)\n    {\n        return [$newChannels];\n    }";

            $content = str_replace($matches[0], $newVia . "\n", $content);
        }
    }

    // 2. Add toWhatsApp method if missing
    if (strpos($content, 'public function toWhatsApp(') === false) {
        $messageExtract = '"إشعار جديد من تطبيق ثمن"';
        if (preg_match("/'message'\s*=>\s*(['\"].*?['\"])/", $content, $m)) {
            $messageExtract = $m[1];
        } elseif (preg_match("/->line\((['\"].*?['\"])\)/", $content, $m)) {
            $messageExtract = $m[1];
        }

        $toWhatsApp = <<<PHP

    public function toWhatsApp(\$notifiable)
    {
        return "تطبيق ثمن 🔔\\n" . $messageExtract;
    }
}
PHP;
        // Replace the last closing brace with the new method
        $content = preg_replace('/\}\s*$/', $toWhatsApp . "\n", $content);
    }

    file_put_contents($file, $content);
    echo "Updated: " . basename($file) . "\n";
}
echo "Done!\n";
