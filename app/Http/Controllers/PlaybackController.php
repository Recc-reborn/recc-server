<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Playback;

class PlaybackController extends Controller
{
    public function store(Request $request)
    {
        return Playback::create([
            'track_id' => $request->track_id,
            'user_id' => $request->user()->id
        ]);
    }

    public function destroy($id)
    {
        Playback::findOrFail($id)->delete();
    }
}
