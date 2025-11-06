<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('ibge_code', 20)->nullable();
            $table->string('name', 60)->nullable(false);
            $table->foreignId('state_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cities');
    }
};
