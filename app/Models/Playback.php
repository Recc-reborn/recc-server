<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playback extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['track_id'];
    public function track()
    {
        return $this->belongsTo(Track::class);
    }
}
