<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('url');
            $table->string('imageable_type');
            $table->string('imageable_id', 24);
            $table->string('company_id', 24)->nullable()->after('id')->index();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
