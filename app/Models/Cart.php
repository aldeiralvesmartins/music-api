<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;


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
        'total',
        'is_active',
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
}
