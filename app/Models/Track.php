<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Track extends Model
{
    use HasFactory;
    use Searchable;

    public $timestamps = false;

    protected $fillable = [
        'artist',
        'duration',
        'title',
        'url'
    ];

    protected $nullable = [
        'album',
        'album_art_url',
    ];

    protected $casts = [
        'duration' => 'integer',
    ];

    public function playbacks()
    {
        return $this->hasMany(Playback::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'track_tag');
    }

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'artist' => $this->artist,
        ];
    }
}
