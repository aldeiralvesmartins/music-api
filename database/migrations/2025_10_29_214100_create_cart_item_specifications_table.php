<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cart_item_specifications', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('cart_item_id');
            $table->string('name');
            $table->string('value');
            $table->string('type')->default('text');
            $table->string('unit')->nullable();
            $table->timestamps();

            $table->foreign('cart_item_id')
                ->references('id')
                ->on('cart_items')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_item_specifications');
    }
};
