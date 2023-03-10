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
            return Playlist::get();
        }
        return response('Unauthenticated', 401);
    }

    public function show($id)
    {
        if (!Auth::check()) {
            return response('Unauthenticated', 401);
        }

        return Playlist::findOrFail($id)->tracks()->get();
    }

    public function create(Request $request)
    {
        if (!Auth::check())
            return response("Unauthenticated", 401);

        $request->validate([
            "title" => "string",
            "tracks" => "array"
        ]);

        $newPlaylist = new Playlist;
        $newPlaylist->user_id = Auth::user()->id;
        $newPlaylist->title = $request->input("title");
        $newPlaylist->origin = PlaylistOrigin::CUSTOM;
        $newPlaylist->save();

        $newPlaylist->tracks()->sync($request->input("tracks"));

        return $newPlaylist;
    }
}
