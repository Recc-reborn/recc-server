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
            'album_art_url' => $request->input("album_art_url"),
            'artist' => $request->input("artist"),
            'duration' => $request->input("duration"),
            'genre' => $request->input("genre"),
            'title' => $request->input("title"),
            'url' => $request->input("url")
        ]);
    }

    public function update($id, Request $request)
    {
        $track = Track::findOrFail($id);
        $track->album_art_url = $request->input("album_art_url");
        $track->artist = $request->input("artist");
        $track->duration = $request->input("duration");
        $track->genre = $request->input("genre");
        $track->title = $request->input("title");
        $track->url = $request->input("url");

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

    private function removeItemFromCollection(Collection $collection, $valueToRemove) {
        foreach ($collection as $key => $value) {
            if ($value == $valueToRemove) {
                $collection->forget($key);
            }
        }
    }

    public function recommendationsByPopularity() {
        $userId = Auth::id();
        $topTracksIds = $this->getTracksIds(self::TOP_SIZE);
        $possibleRecommendations = $topTracksIds->keys();
        $recommendations = collect([]);
        for ($recommendationsMade = 0; $recommendationsMade < self::NUMBER_OF_RECOMMENDATIONS; ++$recommendationsMade) {
            $possibleRecommendation = $possibleRecommendations->random();
            $playbackExists = Playback::where('user_id', $userId)->where('track_id', $possibleRecommendation)->count() > 0;
            if ($playbackExists) {
                $recommendationsMade--;
            } else {
                $recommendations = $recommendations->concat([$possibleRecommendation]);
            }
            $this->removeItemFromCollection($possibleRecommendations, $possibleRecommendation);
            if ($possibleRecommendations->isEmpty() && $recommendationsMade < self::NUMBER_OF_RECOMMENDATIONS) {
                foreach ($recommendations as $recommendation) {
                    $topTracksIds->forget($recommendation);
                }
                $recommendationsLeft = $topTracksIds->keys();
                while ($recommendationsMade < self::NUMBER_OF_RECOMMENDATIONS) {
                    $currentRecommendation = $recommendationsLeft->random();
                    $recommendations = $recommendations->concat([$currentRecommendation]);
                    $this->removeItemFromCollection($recommendationsLeft, $currentRecommendation);
                    $recommendationsMade = $recommendations->count();
                }
            }
        }
        return $recommendations;
    }

}
