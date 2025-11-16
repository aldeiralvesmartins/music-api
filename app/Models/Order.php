<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantScoped;

class Order extends Model
{
    use HasFactory;
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
        'order_id',
        'address_id',
        'total',
        'status',
        'payment_method',
        'company_id',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com o endereço do pedido.
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Pagamento do pedido
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
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
