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

    public function create(Request $request) {

    }

    public function createCustom(ReccService $reccService, Request $request)
    {
        if (!Auth::check())
            return response("Unauthenticated", 401);
        if (empty($request->title))
            return response("\"title\" argument is missing", 400);
        if (empty($request->track_ids))
            return response("\"track_ids\" argument is missing", 400);

        $newPlaylist = new Playlist;
        $newPlaylist->user_id = Auth::user()->id;
        $newPlaylist->title = $request->title;
        $newPlaylist->origin = PlaylistOrigin::CUSTOM;
        $newPlaylist->save();

        $reccService->createCustomPlaylist($request->track_ids, $newPlaylist->id);

        return $newPlaylist;
    }
}
