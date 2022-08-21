<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\Artist;
use App\Models\User;

class PreferredArtistsActionsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $preferredArtistIds;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        // create artists
        $newPreferredArtists = Artist::factory()->count(10)->create();
        // get their IDs for the request
        $this->preferredArtistIds =$newPreferredArtists->pluck('id');
    }

    /**
     * User can set preferred artists.
     * @return void
     */
    public function test_can_set_preferred_artists()
    {

        $this->actingAs($this->user)->postJson(
            route('user.preferred-artists'),
            $this->preferredArtistIds
        );

        $this->assertOk();
    }

    /**
     * The number of preferred artists for this user is the same as the number
     * of artists we initially created
     * @return void
     */
    public function test_preferred_artists_are_correctly_stored()
    {

        $this->actingAs($this->user)->getJson(
            route('user.preferred-artists'),
            $this->preferredArtistIds
        );

        $this->assertOk();
        $this->assertJsonCount(count($this->preferredArtistIds));
    }

    /**
     * User can remove preferred artists.
     * @return void
     */
    public function test_can_remove_preferred_artists()
    {
        $this->actingAs($this->user)->deleteJson(
            route('user.preferred-artists'),
            $this->preferredArtistIds
        );

        $this->assertOk();
    }

    /**
     * Querying the user's preferred artist returns an empty array after
     * removing them all.
     * @return void
     */
    public function test_preferred_artists_are_correctly_removed()
    {
        $this->actingAs($this->user)->getJson(
            route('user.preferred-artists'),
            $this->preferredArtistIds
        );

        $this->assertOk();
        $this->assertJsonCount(0);
    }
}
