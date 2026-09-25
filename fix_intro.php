<?php
$intro = App\Models\Intro::first();
if ($intro) {
    $intro->title_ar = str_replace('ـ', '', $intro->title_ar);
    $intro->save();
    echo "Title fixed successfully!\n";
}
