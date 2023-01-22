<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'set_preferred_artists_at',
        'set_preferred_tracks_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'has_set_preferred_artists',
        'has_set_preferred_tracks'
    ];

    public function preferredArtists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'preferred_artists', 'user_id', 'artist_id');
    }

    public function addPreferredArtists(array $artistIds)
    {
        $this->preferredArtists()->syncWithoutDetaching($artistIds);
        $this->set_preferred_artists_at = now();
        $this->save();
    }

    public function removePreferredArtists(array $artistIds)
    {
        $this->preferredArtists()->detach($artistIds);
    }

    public function hasSetPreferredArtists(): Attribute
    {
        return Attribute::make(
            get: fn () => !empty($this->set_preferred_artists_at)
        );
    }

    public function preferredTracks(): BelongsToMany
    {
        return $this->belongsToMany(Track::class, 'preferred_tracks', 'user_id', 'track_id');
    }

    public function addPreferredTracks(array $tracksIds)
    {
        $this->preferredTracks()->syncWithoutDetaching($tracksIds);
        $this->set_preferred_tracks_at = now();
        $this->save();
    }

    public function removePreferredTracks(array $tracksIds)
    {
        $this->preferredTracks()->detach($tracksIds);
    }

    public function hasSetPreferredTracks(): Attribute
    {
        return Attribute::make(
            get: fn () => !empty($this->set_preferred_tracks_at)
        );
    }
}
