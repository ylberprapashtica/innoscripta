<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestCases\UserTestCases;

class UserPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;
    use UserTestCases;

    /** @test */
    public function it_can_list_user_preferences()
    {
        $numberOfUsers = 5;
        $this->createUsers('test', $numberOfUsers, true);

        $response = $this->actingAsUser()->get('/api/user-preference');

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('total', $numberOfUsers)
            ->etc()
        );
    }

    /** @test */
    public function it_can_create_user_preferences()
    {
        $this->seed(CategorySeeder::class);
        $user = $this->createUser('test');
        $response = $this->actingAs($user)->post('/api/user-preference', [
            "user_id" => $user->id,
            "publisher" => "BBC",
            "categories" => [
                "war"
            ],
            "authors" => [
                "Kathrine Newman"
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_preferences', ['user_id' => $user->id]);
    }

    /** @test */
    public function it_can_update_user_preferences()
    {
        $this->seed(CategorySeeder::class);
        $user = $this->createUserWithPreference('test');
        $response = $this->actingAs($user)->put('/api/user-preference/' . $user->id, [
            "publisher" => "BBC",
            "categories" => [
                "war"
            ],
            "authors" => [
                "Kathrine Newman"
            ]
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_delete_user_preferences()
    {
        $this->seed(CategorySeeder::class);
        $user = $this->createUserWithPreference('test');
        $response = $this->actingAs($user)->delete('/api/user-preference/' . $user->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('user_preferences', ['user_id' => $user->id]);
    }
}
