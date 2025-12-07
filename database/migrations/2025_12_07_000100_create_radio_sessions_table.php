<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radio_sessions', function (Blueprint $table) {
            // Identificador principal da sessão (UUID como string para portabilidade)
            $table->string('id', 36)->primary();

            // Relacionamentos de usuário/sessão anônima e multi-tenant
            $table->string('user_id', 24)->nullable()->index();
            $table->uuid('session_token')->nullable()->index();
            $table->string('company_id', 24)->nullable()->index();

            // Faixa atual e progresso
            $table->string('current_track_id', 24)->nullable()->index();
            $table->decimal('current_track_position', 8, 3)->default(0);

            // Fila e estratégia
            $table->json('play_queue')->nullable();
            $table->string('loop_strategy', 20)->default('no_repeat'); // shuffle | repeat_all | no_repeat

            // Auditoria
            $table->timestamp('last_saved_at')->nullable()->index();
            $table->timestamps();

            // FKs
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('current_track_id')->references('id')->on('songs')->nullOnDelete();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnUpdate()->nullOnDelete();

            // Índices compostos úteis em multi-tenant
            $table->index(['user_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radio_sessions');
    }
};
