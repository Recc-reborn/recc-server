<?php

namespace Database\Seeders;

use App\Models\Playback;
use Illuminate\Database\Seeder;

class PlaybackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Playback::factory()
            ->count(50)
            ->create();
    }
}
