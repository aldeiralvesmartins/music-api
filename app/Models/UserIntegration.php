<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\TenantScoped;

class UserIntegration extends Model
{
    use HasFactory;
    use TenantScoped;

    protected $table = 'user_integrations';
    protected $primaryKey = 'id';
    public $incrementing = false; // id string
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'company_id',
        'category',
        'provider',
        'name', // ← NOVO
        'credentials',
        'settings',
        'is_active',
        'is_default', // ← NOVO
        'connected_at',
        'refreshed_at',
    ];

    protected $casts = [
        'credentials' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean', // ← NOVO
        'connected_at' => 'datetime',
        'refreshed_at' => 'datetime',
    ];


    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = CustomIdService::generateCustomId(get_class($model)));
    }

    /**
     * Relacionamento com o usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope para filtrar por categoria
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope para integrações ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para integração padrão
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Marcar como padrão e desmarcar outras da mesma categoria
     */
    public function markAsDefault()
    {
        // Remove o padrão de outras integrações da mesma categoria e usuário
        self::where('user_id', $this->user_id)
            ->where('category', $this->category)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Marca esta como padrão
        $this->update(['is_default' => true]);
    }
}
