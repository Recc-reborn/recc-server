<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Playback;

class PlaybackController extends Controller
{
    public function store(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            return Playback::create([
                'track_id' => $request->track_id,
                'user_id' => $user->id
            ]);
        }
        return response("Unauthenticated", 401);
    }

    public function destroy(Playback $playback)
    {
        $playback->delete();
    }
}
