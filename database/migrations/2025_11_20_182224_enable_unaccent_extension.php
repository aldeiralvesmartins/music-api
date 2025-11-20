<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Garante instalação da extensão
//        DB::statement("CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA public;");
//
//        // Função imutável para usar em índices
//        DB::statement("
//            CREATE OR REPLACE FUNCTION immutable_unaccent(text)
//            RETURNS text
//            LANGUAGE SQL
//            IMMUTABLE
//            RETURNS NULL ON NULL INPUT
//            AS $$
//                SELECT unaccent($1)
//            $$;
//        ");
//
//        // Índice usando função imutável
//        DB::statement("
//            CREATE INDEX IF NOT EXISTS products_name_unaccent_idx
//            ON products ((immutable_unaccent(lower(name))));
//        ");
    }

    public function down()
    {
//        DB::statement("DROP INDEX IF EXISTS products_name_unaccent_idx;");
//        DB::statement("DROP FUNCTION IF EXISTS immutable_unaccent(text);");
    }
};
