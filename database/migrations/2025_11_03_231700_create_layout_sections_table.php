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
        Schema::create('layout_sections', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name'); // Nome interno da seção (ex: banner_principal, categorias, produtos_destaque)
            $table->string('title')->nullable(); // Título exibido, ex: "Produtos em destaque"
            $table->string('type')->nullable(); // Tipo: banner, produtos, categorias, texto, etc.
            $table->json('content')->nullable(); // JSON com dados personalizados (imagens, ids de produtos, etc)
            $table->integer('position')->default(0); // Ordem de exibição na tela
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layout_sections');
    }
};
