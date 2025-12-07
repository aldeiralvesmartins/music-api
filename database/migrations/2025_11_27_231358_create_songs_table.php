<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->string('id', 24)->primary();

            $table->string('title');
            $table->string('filename');
            $table->string('url');
            $table->string('cover_url')->nullable();

            $table->string('category_id', 24);
            $table->boolean('anuncio')->default(false); // se é anúncio
            $table->decimal('size_mb', 8, 2)->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();

            // hash do arquivo para evitar duplicatas
            $table->string('file_hash', 128)->nullable()->unique();

            // multi company
            $table->string('company_id', 24)->nullable()->index();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
