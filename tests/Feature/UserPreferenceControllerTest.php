<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\TestCases\UserTestCases;

class UserPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;
    use UserTestCases;

    #[Test]
    public function itCanListUserPreferences(): void
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

    #[Test]
    public function itCanCreateUserPreferences(): void
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

    #[Test]
    public function itCanUpdateUserPreferences(): void
    {
        $this->seed(CategorySeeder::class);
        $user = $this->createUserWithPreference('test');
        $response = $this->actingAs($user)->put('/api/user-preference/' . $user->preference->id, [
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

    #[Test]
    public function itCanDeleteUserPreferences()
    {
        $this->seed(CategorySeeder::class);
        $user = $this->createUserWithPreference('test');
        $response = $this->actingAs($user)->delete('/api/user-preference/' . $user->preference->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('user_preferences', ['user_id' => $user->id]);
    }
}
