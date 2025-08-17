<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->string('wallet_id', 24);
            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');

            $table->string('type'); //['deposit', 'withdrawal', 'lock', 'unlock', 'release']
            $table->string('status')->default('pending'); // ['pending', 'held', 'released', 'refunded']
            $table->decimal('amount', 10, 2);

            $table->string('related_id')->nullable(); // para linkar projeto, proposta etc.
            $table->string('related_type')->nullable();
            $table->text('description')->nullable();

            // Campos de pagamento
            $table->string('payment_id')->nullable();
            $table->date('due_date')->nullable();
            $table->date('original_due_date')->nullable();
            $table->string('invoice_url')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('transaction_receipt_url')->nullable();
            $table->string('nosso_numero')->nullable();
            $table->string('bank_slip_url')->nullable();

            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
