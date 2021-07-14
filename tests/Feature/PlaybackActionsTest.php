<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;

use App\Models\Playback;
use App\Models\Track;
use App\Models\User;

class PlaybackActionsTest extends TestCase
{
    public function test_can_generate_playback()
    {
        $user = User::factory()->create();
        $track = Track::factory()->create();

        $response = $this->actingAs($user)
                         ->post(
                             route('playbacks.store'),
                            ['track_id' => $track->id]
                         );

        $response->assertCreated();
    }

    public function test_can_destroy_playback()
    {
        $user = User::factory()->create();
        $track = Track::factory()->create();

        $playback = Playback::create([
            'user_id' => $user->id,
            'track_id' => $track->id,
        ]);

        $response = $this->delete(
            route(
                'playbacks.destroy',
                ['playback' => $playback]
            )
        );

        $response->assertOk();
    }
}
