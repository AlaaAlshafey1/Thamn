<?php
$files = glob(__DIR__.'/app/Notifications/*.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    // Find double closing braces with spaces/newlines between them
    $content = preg_replace("/\}\s*\}/", "}", $content);
    // Specifically fix the via() function double braces:
    // It looks like `    }\n\n    }\n` or `    }\n    }\n`
    $content = str_replace("    }\n\n    }\n", "    }\n\n", $content);
    $content = str_replace("    }\n    }\n", "    }\n", $content);
    
    file_put_contents($file, $content);
    echo "Fixed: " . basename($file) . "\n";
}
