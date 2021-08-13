<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class UserActionsTest extends TestCase
{
    public function test_can_get_self()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('users.get'));

        $response->assertStatus(200);
        $response->assertJson($user->toArray());
    }

    public function test_can_get_index()
    {
        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "*" => [
                "role",
                "email",
                "name",
            ]
        ]);
    }

    public function test_can_create_user()
    {
        $user = User::factory()->make();

        $response = $this->postJson(
            route('users.store'),
            $user->toArray()
        );

        $response->assertCreated();
        $response->assertJsonStructure([
            "name",
            "email",
            "role"
        ]);
    }

    public function test_can_show_user()
    {
        $user = User::factory()->create();

        $response = $this->get(
            route('users.show', ['user' => $user])
        );

        $response->assertJson($user->toArray());
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->delete(
            route('users.destroy', ['user' => $user])
        );

        $response->assertOk();

        // now we have to test if we get a 404 when querying this user
        $response = $this->get(
            route('users.show', ['user' => $user])
        );

        $response->assertNotFound();
    }

    public function test_gets_error_on_wrong_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(
            route(
                'auth.token',
                [
                    'email' => $user->email,
                    'password' => $user->password . 'something_else_so_its_not_correct',
                    'device_name' => 'some_device'
                ]
            )
        );

        // Unprocessable Entity
        $response->assertStatus(422);

        // cleanup
        $user->delete();
    }
}
