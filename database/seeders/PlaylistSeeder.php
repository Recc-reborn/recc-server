<?php

namespace Database\Seeders;

use App\Models\Playlist;
use App\Models\Track;
use Illuminate\Database\Seeder;

class PlaylistSeeder extends Seeder
{
    /**
     * Creates playlists and adds tracks to them
     *
     * @return void
     */
    public function run()
    {
        Playlist::factory()
            ->count(30)
            ->create()
            ->each(function ($playlist) {
                // take 20 random track IDs and put them into this playlist
                $randomTrackIds = Track::limit(20)->inRandomOrder()->get()->pluck("id");
                $playlist->tracks()->attach($randomTrackIds);
            });
    }
}
