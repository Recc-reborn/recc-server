<?php

namespace Database\Factories;

use App\Models\Playback;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlaybackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Playback::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::where('role', 'user')->get()->random();
        $track = Track::get()->random();
        return [
            'user_id' => $user->id,
            'track_id' => $track->id,
        ];
    }
}
