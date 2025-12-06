<?php

namespace App\Models;

use App\Services\CustomIdService;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'description'];

    public function songs()
    {
        return $this->belongsToMany(Song::class, 'playlist_song', 'playlist_id', 'song_id')->withTimestamps();
    }

    public static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id = CustomIdService::generateCustomId(get_class($model)));
    }
}
