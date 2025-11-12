<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Model;

class LayoutSection extends Model
{
    /**
     * A chave primária não é autoincrementável.
     */
    public $incrementing = false;

    /**
     * Tipo da chave primária.
     */
    protected $keyType = 'string';

    protected $fillable = [
        'name', 'title', 'type', 'content', 'position', 'is_active'
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Gera automaticamente o ID antes de criar.
     */
    public static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = CustomIdService::generateCustomId(get_class($model)));
    }
}
