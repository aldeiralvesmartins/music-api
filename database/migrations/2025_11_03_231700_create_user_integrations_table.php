<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_integrations', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->string('category'); // shipping, payment
            $table->string('provider'); // ex: melhor_envio, correios, mercadopago, stripe
            $table->string('name'); // nome personalizado para a integração
            $table->json('credentials'); // tokens, keys, etc
            $table->json('settings')->nullable(); // configs adicionais por integracao
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // ← NOVA COLUNA
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('refreshed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'provider', 'name']); // ← ATUALIZADO
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Novo índice para otimizar consultas
            $table->index(['user_id', 'category', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_integrations');
    }
};
