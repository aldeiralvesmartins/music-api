<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('imageable_type');
            $table->string('imageable_id', 24);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
