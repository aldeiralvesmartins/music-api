<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id');

            // Identificadores e metadados do Asaas
            $table->string('asaas_id')->unique();
            $table->string('object')->nullable();
            $table->date('date_created')->nullable();
            $table->string('customer')->nullable();
            $table->uuid('installment')->nullable();
            $table->string('checkout_session')->nullable();
            $table->string('payment_link')->nullable();

            // Valores
            $table->decimal('value', 12, 2)->nullable();
            $table->decimal('net_value', 12, 2)->nullable();
            $table->decimal('original_value', 12, 2)->nullable();
            $table->decimal('interest_value', 12, 2)->nullable();

            // Descrição e tipo
            $table->text('description')->nullable();
            $table->string('billing_type')->nullable();
            $table->boolean('can_be_paid_after_due_date')->default(false);

            // PIX e status
            $table->json('pix_transaction')->nullable();
            $table->string('status')->nullable();

            // Datas
            $table->date('due_date')->nullable();
            $table->date('original_due_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('client_payment_date')->nullable();

            // Parcela
            $table->integer('installment_number')->nullable();

            // Links e referências
            $table->string('invoice_url')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('external_reference')->nullable()->index();

            // Flags e datas de crédito
            $table->boolean('deleted')->default(false);
            $table->boolean('anticipated')->default(false);
            $table->boolean('anticipable')->default(false);
            $table->date('credit_date')->nullable();
            $table->date('estimated_credit_date')->nullable();
            $table->string('transaction_receipt_url')->nullable();

            // Boleto
            $table->string('nosso_numero')->nullable();
            $table->string('bank_slip_url')->nullable();
            $table->dateTime('last_invoice_viewed_date')->nullable();
            $table->dateTime('last_bank_slip_viewed_date')->nullable();

            // Objetos compostos
            $table->json('discount')->nullable();
            $table->json('fine')->nullable();
            $table->json('interest')->nullable();
            $table->boolean('postal_service')->default(false);
            $table->json('escrow')->nullable();
            $table->json('refunds')->nullable();

            // Payload bruto para auditoria
            $table->json('raw')->nullable();

            $table->timestamps();

               $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
