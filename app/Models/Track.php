<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'artist', 'duration', 'genre', 'album_art_url', 'url'];

    protected $casts = [
        'duration' => 'integer',
    ];

    public function playbacks()
    {
        return $this->hasMany(Playback::class);
    }
}
