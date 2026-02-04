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

        Schema::table('questions', function (Blueprint $table) {
            $table->string('flow')
                ->default('valuation')
                ->after('stageing');
            $table->string('group_type')->default('first')->after('flow');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn([ 'flow','group_type']);
        });
    }
};
