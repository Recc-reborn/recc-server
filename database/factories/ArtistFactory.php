<?php

namespace Database\Factories;

use App\Models\Artist;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArtistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Artist::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->streetName,
            'mbid' => $this->faker->uuid,
            'image_url' => $this->faker->imageUrl(256, 256, null, true, null, false),
            'last_fm_url' => $this->faker->url,
            'listeners' => $this->faker->numberBetween(1000, 10000),
        ];
    }
}
