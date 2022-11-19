<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Playlist;

class RecommendationController extends Controller
{
    public function index()
    {
        /**
         * TODO: Default to FRS and fall back to Playlist index.
         * @see https://recc-reborn.atlassian.net/browse/RECC-61
         */
        $results = Playlist::with('tracks')->get();
        return response()->json(["recommended_playlists" => $results]);
    }
}
