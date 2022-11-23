<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'album',
        'album_art_url',
        'artist',
        'duration',
        'genre',
        'title',
        'url'
    ];

    protected $casts = [
        'duration' => 'integer',
    ];

    public function playbacks()
    {
        return $this->hasMany(Playback::class);
    }
}
