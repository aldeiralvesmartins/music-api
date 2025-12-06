<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Concerns\TenantScoped;

class Category extends Model
{
    use HasFactory;
    use TenantScoped;

    /**
     * A chave primária não é autoincrementável.
     */
    public $incrementing = false;

    /**
     * O tipo da chave primária.
     */
    protected $keyType = 'string';

    /**
     * Campos preenchíveis em massa.
     */
    protected $fillable = [
        'id',
        'name',
        'is_active',
        'slug',
        'description',
        'company_id',
    ];

    /**
     * Casts automáticos.
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Gera o ID automaticamente antes de criar.
     */
    public static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = CustomIdService::generateCustomId(get_class($model)));
    }
}
