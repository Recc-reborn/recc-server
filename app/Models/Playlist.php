<?php

namespace App\Models;

use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    /**
     * Who created the playlist?
     * Can be null. If so, it was auto-generated
     */
    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tracks in this playlist
     */
    public function tracks()
    {
        return $this->belongsToMany(Track::class, "playlist_track");
    }
}
