<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSpecification extends Model
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
        'product_id',
        'name',
        'value',
        'display_value',
        'type',
        'unit',
        'options',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Relacionamento com o produto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Acessor para obter o valor de exibição formatado
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'boolean') {
            return $this->value ? 'Sim' : 'Não';
        }

        if ($this->type === 'number' && $this->unit) {
            return "{$this->value} {$this->unit}";
        }

        return $this->display_value ?? $this->value;
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
