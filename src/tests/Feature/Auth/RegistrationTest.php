<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $timestamp = time();
        $response = $this->post('/register', [
            'name' => 'Test User ' . $timestamp,
            'email' => 'testuser' . $timestamp . '@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register/complete');
        $this->assertDatabaseHas('users', [
            'name' => 'Test User ' . $timestamp,
            'email' => 'testuser' . $timestamp . '@example.com',
        ]);
    }
}
