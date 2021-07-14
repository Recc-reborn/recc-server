<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;

use App\Models\Track;

class TrackActionsTest extends TestCase
{
    public function test_can_get_track_index()
    {
        $response = $this->get(route('tracks.index'));

        $response->assertOk();
    }

    public function test_can_create_track()
    {
        $newTrack = Track::factory()->make();
        $response = $this->post(route('tracks.store', $newTrack->toArray()));

        $response->assertCreated();
        $response->assertJson($newTrack->toArray());

        $newTrack->delete();
    }

    public function test_can_show_track()
    {
        // creates a new track
        $newTrack = Track::factory()->create();

        // attempts to get track from controller
        $response = $this->get(route('tracks.show', ['track' => $newTrack]));

        $response->assertOk();
        // compares controller track ID to created track ID
        $response->assertJson($newTrack->toArray());

        $newTrack->delete();
    }

    public function test_can_delete_track()
    {
        $newTrack = Track::factory()->create();

        $deleteResponse = $this->delete(route('tracks.destroy', $newTrack));
        $deleteResponse->assertStatus(200);

        $notFoundResponse = $this->get(route('tracks.show', ['track' => $newTrack]));
        $notFoundResponse->assertNotFound();
    }
}
