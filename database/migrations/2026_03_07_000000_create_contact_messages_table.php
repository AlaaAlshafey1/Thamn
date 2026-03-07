<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('contact_messages', function (Blueprint $row) {
            $row->id();
            $row->string('name');
            $row->string('email');
            $row->string('subject')->nullable();
            $row->text('message');
            $row->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_messages');
    }
};
