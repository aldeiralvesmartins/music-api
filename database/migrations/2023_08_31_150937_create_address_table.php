<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->string('id', 24)->unique();
            $table->string('zip_code', 10);
            $table->string('street', 80)->nullable();
            $table->string('number', 10)->nullable();
            $table->string('neighborhood', 255)->nullable();
            $table->string('complement', 255)->nullable();
            $table->string('user_id', 24);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
