<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Artist extends Model
{
    use Searchable;
    use HasFactory;

    // No need to know when an artist was added
    public $timestamps = false;

    // filled by \App\Console\Commands\CloneLastFMArtists.php
    public $fillable = [
        'image_url',
        'last_fm_url',
        'listeners',
        'mbid',
        'name',
    ];

    public function toSearchableArray()
    {
        return ["name" => $this->name];
    }

    public function tags() {
        return $this->hasMany(Tag::class);
    }

}
