<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantScoped;

class CartItem extends Model
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
        'product_id',
        'company_id',
        'quantity',
        'price',
        'subtotal',
        'specifications',
    ];

    protected $casts = [
        'specifications' => 'array',
    ];

    /**
     * Usuário dono do item do carrinho
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Produto associado ao item do carrinho
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Especificações do item do carrinho
     */
    public function specifications()
    {
        return $this->hasMany(CartItemSpecification::class);
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
