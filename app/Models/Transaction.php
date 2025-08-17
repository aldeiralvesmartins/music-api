<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    public $incrementing = false; // porque o id é string

    protected $keyType = 'string';
    protected $fillable = [
        'wallet_id',
        'type',
        'status',               // novo
        'amount',
        'related_id',
        'related_type',
        'description',
        'payment_id',           // novos campos de pagamento
        'due_date',
        'original_due_date',
        'invoice_url',
        'invoice_number',
        'transaction_receipt_url',
        'nosso_numero',
        'bank_slip_url',
    ];


    // Relacionamento com Wallet
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function related()
    {
        return $this->morphTo();
    }
}
