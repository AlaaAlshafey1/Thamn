<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arbitrator_declarations', function (Blueprint $table) {
            $table->string('nationality')->nullable()->after('email');
            $table->string('city')->nullable()->after('nationality');
            $table->string('expertise')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('arbitrator_declarations', function (Blueprint $table) {
            $table->dropColumn(['nationality', 'city', 'expertise']);
        });
    }
};
