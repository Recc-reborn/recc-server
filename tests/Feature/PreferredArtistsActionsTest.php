<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\Artist;
use App\Models\User;
use Database\Seeders\ArtistsTableSeeder;

class PreferredArtistsActionsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $preferredArtistIds;

    public function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->seed(ArtistsTableSeeder::class);

        $this->preferredArtistIds =
            Artist::all()
             ->random(5)
             ->pluck('id')
             ->toArray();
    }

    /**
     * User can set preferred artists.
     * @return void
     */
    public function test_can_set_preferred_artists()
    {
        $response = $this->actingAs($this->user)->patchJson(
            route('user.preferred-artists'),
            $this->preferredArtistIds
        );

        $response->assertOk();

        $response = $this->actingAs($this->user)->getJson(
            route('user.preferred-artists')
        );

        $response->assertOk();
        $response->assertJson($this->preferredArtistIds);
    }

    /**
     * User can remove preferred artists.
     * @return void
     */
    public function test_can_remove_preferred_artists()
    {
        $this->user->addPreferredArtists($this->preferredArtistIds);

        $response = $this->actingAs($this->user)->deleteJson(
            route('user.preferred-artists'),
            $this->preferredArtistIds
        );

        $response->assertOk();
    }

    /**
     * Querying the user's preferred artist returns an empty array after
     * removing them all.
     * @return void
     */
    public function test_preferred_artists_are_correctly_removed()
    {
        $response = $this->actingAs($this->user)->getJson(
            route('user.preferred-artists')
        );

        $response->assertOk();
        $response->assertJsonCount(0);
    }
}
