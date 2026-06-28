<?php
$files = glob(__DIR__ . '/app/Notifications/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Replace via
    $content = str_replace(
        "return ['database'];",
        "return ['database', 'mail', \App\Channels\WhatsAppChannel::class];",
        $content
    );
    $content = str_replace(
        "return ['database', 'mail'];",
        "return ['database', 'mail', \App\Channels\WhatsAppChannel::class];",
        $content
    );
    $content = str_replace(
        "return ['mail', 'database'];",
        "return ['database', 'mail', \App\Channels\WhatsAppChannel::class];",
        $content
    );

    // Get message line
    $messageExtract = '"إشعار جديد من منصة ثمن"';
    if (preg_match("/'message'\s*=>\s*(['\"].*?['\"])/", $content, $m)) {
        $messageExtract = $m[1];
    } elseif (preg_match("/->line\((['\"].*?['\"])\)/", $content, $m)) {
        $messageExtract = $m[1];
    }
    
    // Add WhatsApp
    if (strpos($content, 'function toWhatsApp') === false) {
        $toWhatsApp = <<<PHP

    public function toWhatsApp(\$notifiable)
    {
        return "منصة ثمن 🔔\\n" . $messageExtract;
    }
}
PHP;
        // Find last closing brace
        $pos = strrpos($content, '}');
        if ($pos !== false) {
            $content = substr_replace($content, $toWhatsApp, $pos, strlen($content) - $pos);
        }
    }
    
    file_put_contents($file, $content);
    echo "Fixed: " . basename($file) . "\n";
}
