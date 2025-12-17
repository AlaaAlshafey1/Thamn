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
        Schema::table('question_options', function (Blueprint $table) {
            $table->string('price')->nullable()->after('max');
            $table->string('badge')->nullable()->after('price');
            $table->string('sub_options_title')->nullable()->after('badge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn(['price', 'badge', 'sub_options_title']);
        });
    }
};
