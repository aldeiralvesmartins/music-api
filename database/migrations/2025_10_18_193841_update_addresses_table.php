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
        Schema::table('addresses', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('user_id');
            $table->string('state', 2)->after('city_id');
            $table->string('country', 100)->default('Brasil')->after('state');
            $table->string('reference', 255)->nullable()->after('complement');

            // Update existing columns to match our requirements
            $table->string('zip_code', 10)->change();
            $table->string('street', 255)->change();
            $table->string('number', 20)->change();
            $table->string('city', 200);


            // Add foreign key with proper constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['is_default', 'state', 'country', 'reference']);

            // Revert column changes
            $table->string('zip_code', 10)->change();
            $table->string('street', 80)->change();
            $table->string('number', 10)->change();
            $table->foreignId('city_id')->change();

            // Remove foreign key
            $table->dropForeign(['user_id']);
        });
    }
};
