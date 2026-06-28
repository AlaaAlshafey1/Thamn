<?php
$content = file_get_contents(__DIR__.'/storage/app/firebase/firebase_credentials.json');
echo base64_encode($content) . "\n";
