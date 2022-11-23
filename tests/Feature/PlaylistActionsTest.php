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

class PlaylistActionsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $tracks;
    protected $trackIds;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tracks = Track::factory()->count(10)->create();
        $this->trackIds = $this->tracks->pluck('id');
    }

    public function test_can_create_playlist()
    {
        $requestBody = ["title" => "Whatever", "tracks" => $this->trackIds];

        $response = $this->actingAs($this->user)->postJson(
            route('playlists.create'),
            $requestBody,
        );

        $response->assertCreated();

        $latestCreatedPlaylist = Playlist::last();

        $response->assertSimilarJson($latestCreatedPlaylist);
    }

    public function test_gets_401_when_unauthorized()
    {
        $requestBody = ["title" => "Whatever", "tracks" => $this->trackIds];

        $response = $this->postJson(route('playlists.create'), $requestBody);

        $response->assertUnauthorized();
    }
}
