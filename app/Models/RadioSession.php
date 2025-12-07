<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RadioSession extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'session_token',
        'current_track_id',
        'current_track_position',
        'play_queue',
        'loop_strategy',
        'last_saved_at',
        'company_id',
    ];

    protected $casts = [
        'play_queue' => 'array',
        'last_saved_at' => 'datetime',
        'current_track_position' => 'decimal:3',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentTrack(): BelongsTo
    {
        return $this->belongsTo(Song::class, 'current_track_id');
    }

    public function playedSongs(): HasMany
    {
        return $this->hasMany(PlayedSong::class, 'session_token', 'session_token');
    }
}
