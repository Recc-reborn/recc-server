<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Playback;

class PlaybackController extends Controller
{
    public function store(Request $request)
    {
        return Playback::create([
            'trackId' => $request->trackId
        ]);
    }

    public function destroy($id)
    {
        Playback::findOrFail($id)->delete();
    }
}
