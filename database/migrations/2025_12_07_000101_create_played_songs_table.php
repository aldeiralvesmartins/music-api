<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('played_songs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Identidade do ouvinte (um dos dois)
            $table->string('user_id', 24)->nullable()->index();
            $table->uuid('session_token')->nullable()->index();

            // Música e multi-tenant
            $table->string('song_id', 24)->index();
            $table->string('company_id', 24)->nullable()->index();

            // Metadados de play
            $table->timestamp('played_at')->useCurrent()->index();
            $table->unsignedInteger('duration_played')->nullable();

            $table->timestamps();

            // FKs
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('song_id')->references('id')->on('songs')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnUpdate()->nullOnDelete();

            // Índices compostos para evitar repetição e acelerar consultas
            $table->index(['user_id', 'song_id']);
            $table->index(['session_token', 'song_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('played_songs');
    }
};
