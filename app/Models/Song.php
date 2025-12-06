<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $primaryKey = 'id';

    public $incrementing = false; // porque o id é string

    protected $keyType = 'string';
    protected $fillable = ['title', 'filename', 'url', 'cover_url', 'anuncio', 'category_id', 'size_mb', 'duration_seconds'];

    protected $casts = [
        'anuncio' => 'boolean',
        'size_mb' => 'float',
        'duration_seconds' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = CustomIdService::generateCustomId(get_class($model)));
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_song', 'song_id', 'playlist_id')->withTimestamps();
    }
}
