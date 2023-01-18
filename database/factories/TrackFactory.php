<?php

namespace Database\Factories;

use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Track::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $fakeTags = [
            'Art Electro Prog',
            'Electro Norteño',
            'Gangsta EDM',
            'Gazecore',
            'Hardcore',
            'Post-Lofi',
            'Space Latin',
            'Shoegaze',
            'Woke Twerk'
        ];

        return [
            'title' => $this->faker->realText($maxNbChars=50),
            'artist' => $this->faker->firstName() . ' ' . $this->faker->lastName(),
            'duration' => $this->faker->numberBetween(60, 600),
            'album' => $this->faker->realText($maxNbChars=30),
            'album_art_url' => "https://place.dog/255/255",
            'url' => $this->faker->url(),
        ];
    }
}
