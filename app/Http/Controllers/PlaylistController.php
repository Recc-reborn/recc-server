<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Playlist;
use App\Enums\PlaylistOrigin;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            return Playlist::get();
        }
        return response('Unauthenticated', 401);
    }

    public function create(Request $request)
    {
        $request->validate([
            "title" => "string",
            "tracks" => "array"
        ]);

        $newPlaylist = new Playlist;
        $newPlaylist->user_id = $request->user()->id;
        $newPlaylist->title = $request->input("title");
        $newPlaylist->origin = PlaylistOrigin::CUSTOM;
        $newPlaylist->save();

        $newPlaylist->tracks()->sync($request->input("tracks"));

        return $newPlaylist;
    }
}
