<?php

namespace Tests\Feature;

use Tests\TestCase;

class ArtistActions extends TestCase
{
    public function test_can_list_all()
    {
        $response = $this->get(route('artists.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                // artists array
                '*' => [
                    'name'
                ]
            ],
        ]);
    }
}
