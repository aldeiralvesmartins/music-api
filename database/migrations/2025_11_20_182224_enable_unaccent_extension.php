<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Garante instalação da extensão no schema public
        DB::statement("CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA public;");

        // Índice funcional sem especificar schema
        DB::statement("
            CREATE INDEX IF NOT EXISTS products_name_unaccent_idx
            ON products ((unaccent(lower(name))));
        ");
    }

    public function down()
    {
        DB::statement("DROP INDEX IF EXISTS products_name_unaccent_idx;");
    }
};
