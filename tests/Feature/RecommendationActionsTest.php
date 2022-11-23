<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

use App\Models\Track;
use App\Models\User;
use App\Models\Playlist;

class RecommendationActionsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $availablePlaylistCount = 3;

    public function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Track::factory()->count(100)->create();
        Playlist::factory()
            ->count($this->availablePlaylistCount)
            ->create()
            ->each(function ($playlist) {
                $trackIds = Track::limit(20)
                    ->inRandomOrder()
                    ->get('id')
                    ->pluck('id');
                $playlist->tracks()->attach($trackIds);
            });
    }

    /**
     * User can get all playlists
     * TODO: Change different playlist origins
     * @return void
     */
    public function test_can_get_recommendations()
    {
        $response = $this->actingAs($this->user)->getJson(
            route('reccs.index')
        );

        $response->assertOk();

        // validate playlist count
        $response->assertJsonCount($this->availablePlaylistCount);
    }

    public function test_gets_401_when_unauthorized()
    {
        $response = $this->getJson(route('reccs.index'));

        $response->assertUnauthorized();
    }
}
