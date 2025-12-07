<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 24)->primary();

            // campos básicos
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('document', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('avatar')->nullable();

            // permissões
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_super_admin')->default(false);

            // tipo e dados
            $table->string('type')->default('client');
            $table->text('bio')->nullable();
            $table->json('portfolio')->nullable();
            $table->string('photo')->nullable();
            $table->string('taxpayer')->nullable()->unique();

            // relacionamento empresa
            $table->string('company_id', 24)->nullable()->index();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnUpdate()->nullOnDelete();

            // customer para ASAAS
            $table->string('customer_id')->nullable();

            // timestamps padrão
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id', 24)->nullable()->index();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
