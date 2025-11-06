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
        Schema::table('users', function (Blueprint $table) {
            // Add new fields
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('document', 20)->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('document');
            $table->string('avatar')->nullable()->after('birth_date');
            $table->boolean('is_admin')->default(false)->after('avatar');
            
            // Update existing fields
            $table->string('password')->nullable()->change();
            $table->string('email')->nullable()->change();
            
            // Add remember token if not exists
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
            
            // Add email verification timestamp if not exists
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'document',
                'birth_date',
                'avatar',
                'is_admin'
            ]);
            
            // Revert changes to existing fields
            $table->string('password')->change();
            $table->string('email')->change();
        });
    }
};
