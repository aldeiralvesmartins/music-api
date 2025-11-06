<?php

namespace App\Models;

use App\Services\CustomIdService;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;
    protected $primaryKey = 'id';

    public $incrementing = false; // porque o id é string

    protected $keyType = 'string';

    /**
     * Get the addresses for the user.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the default address for the user.
     */
    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    /**
     * Get the orders for the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'document',
        'birth_date',
        'bio',
        'type',
        'avatar',
        'city_id',
        'email_verified_at',
        'is_admin'
    ];

    protected $with = ['addresses'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = CustomIdService::generateCustomId(get_class($model)));
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * User integrations (dynamic external providers)
     */
    public function integrations(): HasMany
    {
        return $this->hasMany(UserIntegration::class);
    }


}
