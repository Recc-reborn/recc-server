<?php

namespace Database\Factories;

use App\Models\Playlist;
use App\Enums\PlaylistOrigin;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlaylistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Playlist::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText($maxNbChars=50),
            'origin' => PlaylistOrigin::RECOMMENDED_FAVORITES,
        ];
    }
}
