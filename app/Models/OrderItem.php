<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    /**
     * A chave primária não é autoincrementável.
     */
    public $incrementing = false;

    /**
     * Tipo da chave primária.
     */
    protected $keyType = 'string';
    // Permite atribuição em massa
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    /**
     * Relacionamento com o pedido.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relacionamento com o produto.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Gera automaticamente o ID antes de criar.
     */
    public static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = CustomIdService::generateCustomId(get_class($model)));
    }
}
