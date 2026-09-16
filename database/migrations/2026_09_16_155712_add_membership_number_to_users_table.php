<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // رقم العضوية: يُولَّد تلقائياً عند التسجيل ويكون فريداً لكل مستخدم/خبير
            if (!Schema::hasColumn('users', 'membership_number')) {
                $table->string('membership_number', 20)->nullable()->unique()->after('role_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('membership_number');
        });
    }
};
