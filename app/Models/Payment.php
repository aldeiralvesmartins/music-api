<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantScoped;

class Payment extends Model
{
    use TenantScoped;
    /**
     * A chave primária não é autoincrementável.
     */
    public $incrementing = false;

    /**
     * Tipo da chave primária.
     */
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'cart_id',
        'company_id',
        'asaas_id',
        'object',
        'date_created',
        'customer',
        'installment',
        'checkout_session',
        'payment_link',
        'value',
        'net_value',
        'original_value',
        'interest_value',
        'description',
        'billing_type',
        'can_be_paid_after_due_date',
        'pix_transaction',
        'status',
        'due_date',
        'original_due_date',
        'payment_date',
        'client_payment_date',
        'installment_number',
        'invoice_url',
        'invoice_number',
        'external_reference',
        'deleted',
        'anticipated',
        'anticipable',
        'credit_date',
        'estimated_credit_date',
        'transaction_receipt_url',
        'nosso_numero',
        'bank_slip_url',
        'last_invoice_viewed_date',
        'last_bank_slip_viewed_date',
        'discount',
        'fine',
        'interest',
        'postal_service',
        'escrow',
        'refunds',
        'raw',
    ];

    protected $casts = [
        'date_created' => 'date',
        'due_date' => 'date',
        'original_due_date' => 'date',
        'payment_date' => 'date',
        'client_payment_date' => 'date',
        'credit_date' => 'date',
        'estimated_credit_date' => 'date',
        'last_invoice_viewed_date' => 'datetime',
        'last_bank_slip_viewed_date' => 'datetime',
        'value' => 'decimal:2',
        'net_value' => 'decimal:2',
        'original_value' => 'decimal:2',
        'interest_value' => 'decimal:2',
        'can_be_paid_after_due_date' => 'boolean',
        'deleted' => 'boolean',
        'anticipated' => 'boolean',
        'anticipable' => 'boolean',
        'postal_service' => 'boolean',
        'installment_number' => 'integer',
        'pix_transaction' => 'array',
        'discount' => 'array',
        'fine' => 'array',
        'interest' => 'array',
        'escrow' => 'array',
        'refunds' => 'array',
        'raw' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

        /**
     * Gera automaticamente o ID antes de criar.
     */
    public static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = CustomIdService::generateCustomId(get_class($model)));
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
