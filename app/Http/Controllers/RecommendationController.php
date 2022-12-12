<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Playlist;

class RecommendationController extends Controller
{
    public function index()
    {
        // TODO implement different recommendation strategies,
        // and use this as a fallback
        return Playlist::get();
    }
}
