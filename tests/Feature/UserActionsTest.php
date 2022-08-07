<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
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
            [
                "email" => $user->email,
                "name" => $user->name,
                "password" => $user->password
            ]
        );

        $response->assertCreated();
        $response->assertJsonStructure([
            "name",
            "email",
            "role"
        ]);
    }

    public function test_validates_user_email()
    {
        // Create a new user
        $firstUserWithThisEmail = User::factory()->create();

        // Try to register a new user with the same email
        $response = $this->postJson(
            route('users.store'),
            [
                "name" => "John Doe",
                "email" => $firstUserWithThisEmail->email,
                "password" => "SuperSecret"
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure([
            "message",
            "errors" => ["email"],
        ]);
    }

    public function test_validates_user_store_request()
    {
        $response = $this->postJson(
            route('users.store'),
            [
                "email" => "",
            ]
        );

        $response->assertStatus(422);
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

    public function test_can_log_in()
    {
        $user = User::factory()->make();
        $user->password = Hash::make('123456789');
        $user->save();

        $response = $this->postJson(
            route(
                'auth.token',
                [
                    'email' => $user->email,
                    'password' => '123456789',
                    'device_name' => 'some_device'
                ]
            )
        );

        $response->assertOk();
        $response->assertJsonStructure(['token']);
    }

    public function test_gets_error_on_wrong_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(
            route(
                'auth.token',
                [
                    'email' => $user->email,
                    'password' => 'some_incorrect_password',
                    'device_name' => 'some_device'
                ]
            )
        );

        $response->assertStatus(422);
    }
}
