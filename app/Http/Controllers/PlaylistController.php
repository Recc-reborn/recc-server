<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Playlist;
use App\Enums\PlaylistOrigin;
use Illuminate\Support\Facades\Auth;
use App\Services\ReccService;

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

    public function me()
    {
        if (!Auth::check()) {
            return response('Unauthenticated', 401);
        }
        $user = Auth::user();
        return $playlist = Playlist::where("user_id", $user->id)->get();
    }

    public function create(ReccService $reccService) {
        if (!Auth::check())
            return response("Unauthenticated", 401);
        $user = Auth::user();
        $reccService->createAutoPlaylist($user->id);
    }

    public function createCustom(ReccService $reccService, Request $request) {
        if (!Auth::check())
            return response("Unauthenticated", 401);

        $request->validate([
            "title" => "string",
            "track_ids" => "array"
        ]);

        $newPlaylist = new Playlist;
        $newPlaylist->user_id = Auth::user()->id;
        $newPlaylist->title = $request->input("title");
        $newPlaylist->origin = PlaylistOrigin::CUSTOM;
        $newPlaylist->save();

        $reccService->createCustomPlaylist($request->input("track_ids"), $newPlaylist->id);

        return $newPlaylist;
    }
}
