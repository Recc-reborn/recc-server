<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Playback;

class PlaybackController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check())
            return response("Unauthenticated", 401);

        $request->validate([
            "track_id" => "int"
        ]);

        $user = Auth::user();
        return Playback::create([
            'track_id' => $request->input('track_id'),
            'user_id' => $user->id
        ]);
    }

    public function destroy(Playback $playback)
    {
        $playback->delete();
    }
}
