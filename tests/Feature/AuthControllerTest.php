<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\TestCases\UserTestCases;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    use UserTestCases;

    #[Test]
    public function userCanRegister(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/api/register', $userData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'user@example.com']);
    }

    #[Test]
    public function userCanLogin(): void
    {
        $userModel = $this->createUser('user');

        $response = $this->get('/sanctum/csrf-cookie');

        $response = $this->post('/api/login', [
            'email' => $userModel->getAttribute('email'),
            'password' => 'password',
        ], [
            'Accept' => 'application/json',
            'X-XSRF-TOKEN' => $response->getCookie('XSRF-TOKEN')->getValue()
        ]);

        $response->assertStatus(200);
        $response->assertCookie('laravel_session');
    }
}
