<?php

namespace Tests\TestCases;


use App\Models\User;
use App\Models\UserPreference;

trait UserTestCases
{
    const USER_ATTRIBUTES_TO_CHANGE = [
        'name'
    ];

    private function actingAsUser(User $user = null)
    {
        if ($user) {
            $this->actingAs($user);
        } else {
            $this->actingAs($this->createUser('user'));
        }
        return $this;
    }

    private function createUser(string $name): User
    {
        $userData = [
            'name' => $name,
            'email' => $name . '@innoscripta.com',
            'password' => bcrypt('password'),
        ];

        return User::create($userData);
    }

    private function changeCurrentUserValues(array $attributes, int $count): array
    {
        $subset = array_intersect_key($attributes, array_flip(self::USER_ATTRIBUTES_TO_CHANGE));
        return array_merge($attributes, array_map(fn($value) => $value . '-' . $count, $subset));
    }

    private function createUsers(
        string $name,
        int    $numberOfUsersToCreate = 1,
        bool   $withPreferences = false
    ): void
    {
        for ($i = 0; $i < $numberOfUsersToCreate; $i++) {
            if ($withPreferences) {
                $this->createUserWithPreference($name . '-' . $i);
            } else {
                $this->createUser($name . '-' . $i);
            }
        }
    }

    private function createUserWithPreference(string $name): User
    {
        $user = $this->createUser($name);
        UserPreference::factory()->create(['user_id' => $user->id]);
        return $user;
    }
}
