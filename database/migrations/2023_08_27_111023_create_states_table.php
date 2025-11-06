<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('acronyms', 2)->nullable(false);
            $table->string('name', 30)->nullable(false);
            $table->foreignId('country_id')->references('id')->on('countries')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('states');
    }
};
