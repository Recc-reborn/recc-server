<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    // No need in knowing when the Tag has heen added
    public $timestamps = false;

    protected $fillable = [
        'name',
        'url',
    ];

    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'track_tag');
    }
}
