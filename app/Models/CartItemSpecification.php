<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantScoped;

class CartItemSpecification extends Model
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
        'cart_item_id',
        'company_id',
        'name',
        'value',
        'type',
        'unit',
    ];

    /**
     * Item do carrinho associado à especificação
     */
    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

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
