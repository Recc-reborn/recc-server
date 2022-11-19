<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Playlist;
use App\Models\User;

class ReccsActionsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $expectedPlaylistCount = 10;

    public function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Playlist::factory()->count($this->expectedPlaylistCount)->create();
    }

    public function test_gets_all_playlists_with_tracks()
    {
        $expectedPlaylists = Playlist::with('tracks')->get()->toArray();
        $response = $this
            ->actingAs($this->user)
            ->getJson(route('reccs.index'));

        $response->assertOk();

        $response->assertJson([
            "recommended_playlists" => $expectedPlaylists,
        ]);
    }

    public function test_throws_error_when_unauthenticated()
    {
        $response = $this->getJson(route('reccs.index'));

        $response->assertUnauthorized();
    }
}
