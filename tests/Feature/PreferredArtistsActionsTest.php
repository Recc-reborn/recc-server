<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
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
        Artist::factory()->count(100)->create();

        $this->preferredArtistIds =
            Artist::all()
             ->random(5)
             ->pluck('last_fm_url')
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

        // validate preferred artist count
        $response->assertJsonCount(count($this->preferredArtistIds));

        // validate preferred artist ids match those in the request
        $responseBody = $response->json();
        foreach ($this->preferredArtistIds as $preferredArtistId) {
            $this->assertContains($preferredArtistId, $responseBody);
        }
    }

    /**
     * User can remove preferred artists.
     * @return void
     */
    public function test_can_remove_preferred_artists()
    {
        // first, set preferred artists
        $this->actingAs($this->user)->patchJson(
            route('user.preferred-artists'),
            $this->preferredArtistIds
        );

        // so that we can then remove the first one
        $response = $this->actingAs($this->user)->deleteJson(
            route('user.preferred-artists'),
            [$this->preferredArtistIds[0]]
        );

        $response->assertOk();
    }
}
