<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->string('id', 24)->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->unique();
            $table->enum('type', ['subdomain', 'custom_domain'])->default('subdomain');
            $table->string('owner_id', 24)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
