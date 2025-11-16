<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantScoped;

class Cart extends Model
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
        'company_id',
        'total',
        'is_active',
        'payment_method',
        'shipping_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = CustomIdService::generateCustomId(get_class($model)));
    }

    protected $casts = [
        'payment_method' => 'array',
        'shipping_method' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
