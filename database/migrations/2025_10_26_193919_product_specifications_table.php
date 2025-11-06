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
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->string('id', 24)->unique();
            $table->string('product_id', 24);
            $table->string('name'); // Ex: "Tamanho", "Cor", "Material"
            $table->string('value'); // Valor principal (pode ser o ID da opção selecionada ou o valor bruto)
            $table->string('display_value')->nullable(); // Valor para exibição (opcional)
            $table->enum('type', ['text', 'number', 'select', 'boolean', 'color'])->default('text');
            $table->string('unit')->nullable(); // Unidade de medida (cm, kg, L, etc)
            $table->json('options')->nullable(); // Opções para tipo 'select'
            $table->integer('sort_order')->default(0); // Ordem de exibição
            $table->timestamps();
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
            // Índices para melhorar performance de buscas
            $table->index(['product_id', 'name']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_specifications');
    }
};
