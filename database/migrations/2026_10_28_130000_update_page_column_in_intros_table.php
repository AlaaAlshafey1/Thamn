<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // تغيير نوع عمود page من enum إلى varchar(100) لدعم الصفحات الجديدة والأنواع المخصصة
        DB::statement("ALTER TABLE intros MODIFY COLUMN page VARCHAR(100) NOT NULL;");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE intros MODIFY COLUMN page ENUM('welcome', 'login', 'signup', 'verify') NOT NULL;");
    }
};
