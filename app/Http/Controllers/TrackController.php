<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Track;

class TrackController extends Controller
{
    public function index()
    {
        return Track::all();
    }

    public function show($id)
    {
        return Track::findOrFail($id);
    }

    public function store(Request $request)
    {
        return Track::create([
            'title' => $request->title,
            'artist' => $request->artist,
            'duration' => $request->duration,
            'genre' => $request->genre
        ]);
    }

    public function update($id, Request $request)
    {
        $track = Track::findOrFail($id);
        $track->title = $request->title;
        $track->artist = $request->artist;
        $track->duration = $request->duration;
        $track->genre = $request->genre;

        $track->save();
    }

    public function destroy($id)
    {
        Track::findOrFail($id)->delete();
    }
}
