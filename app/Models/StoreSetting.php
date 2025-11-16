<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\TenantScoped;

class StoreSetting extends Model
{
    use TenantScoped;
    /**
     * Primary key is not auto-incrementing.
     */
    public $incrementing = false;

    /**
     * Primary key type.
     */
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'company_id',
        'store_name',
        'primary_color',
        'secondary_color',
        'background_color',
        'text_color',
        'font_family',
        'logo_url',
        'favicon_url',
        'custom_css',
        'custom_js',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = CustomIdService::generateCustomId(get_class($model)));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
