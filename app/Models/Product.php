<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Concerns\TenantScoped;

class Product extends Model
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

    /**
     * Campos preenchíveis em massa.
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'price',
        'image',
        'category_id',
        'stock',
        'is_active',
        'company_id',
    ];

    /**
     * Casts automáticos.
     */
    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Gera automaticamente o ID antes de criar.
     */
    public static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = CustomIdService::generateCustomId(get_class($model)));
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Relacionamento com as especificações do produto
     */
    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('sort_order');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
