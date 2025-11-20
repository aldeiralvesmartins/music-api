<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Índice funcional no campo 'name' usando public.unaccent
        DB::statement('
            CREATE INDEX IF NOT EXISTS products_name_unaccent_idx
            ON products ((public.unaccent(lower(name::text))));
        ');

        // Índice funcional no campo 'description'
        DB::statement('
            CREATE INDEX IF NOT EXISTS products_description_unaccent_idx
            ON products ((public.unaccent(lower(description::text))));
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS products_name_unaccent_idx;');
        DB::statement('DROP INDEX IF EXISTS products_description_unaccent_idx;');
    }
};
