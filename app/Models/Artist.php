<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    // No need to know when an artist was added
    public $timestamps = false;

    // filled by \App\Console\Commands\CloneLastFMArtists.php
    public $fillable = ['name'];
}
