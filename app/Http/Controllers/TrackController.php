<?php

namespace App\Http\Controllers;

use App\Models\Playback;
use Illuminate\Http\Request;

use App\Models\Track;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TrackController extends Controller
{
    const NUMBER_OF_RECOMMENDATIONS = 5;
    const TOP_SIZE = 20;

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

    private function getTracksIds(int $limit = 0) {
        $ids = Playback::all()->countBy('track_id')->sortDesc();
        if ($limit) {
            $ids = $ids->take($limit);
        }
        return $ids;
    }

    private function getTracks(Collection $tracks_ids)
    {
        $tracks = collect([]);
        foreach ($tracks_ids->toArray() as $key => $value) {
            $tracks = $tracks->concat(Track::where('id', $key)->get());
        }
        return $tracks;
    }

    public function allTimePopulars() {
        return $this->getTracks($this->getTracksIds());
    }

    public function allTimeTop($top) {
        return $this->getTracks($this->getTracksIds($top));
    }

}
