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
        Schema::table('home_steps', function (Blueprint $table) {
            $table->enum('type', ['steps', 'check', 'image', 'banner'])->default('steps')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_steps', function (Blueprint $table) {
            $table->enum('type', ['steps', 'check', 'image'])->default('steps')->change();
        });
    }
};
