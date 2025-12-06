<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->decimal('size_mb', 8, 2)->nullable()->after('anuncio');
            $table->unsignedInteger('duration_seconds')->nullable()->after('size_mb');
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['size_mb', 'duration_seconds']);
        });
    }
};
