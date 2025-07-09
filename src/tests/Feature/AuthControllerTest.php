<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_register_page_can_be_rendered()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_user_can_register_with_valid_data()
    {
        $timestamp = time();
        $response = $this->post('/register', [
            'name' => 'Test User ' . $timestamp,
            'email' => 'testuser' . $timestamp . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('http://localhost/register/complete');
        $this->assertDatabaseHas('users', [
            'name' => 'Test User ' . $timestamp,
            'email' => 'testuser' . $timestamp . '@example.com',
        ]);
    }

    public function test_login_page_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'test_user1@example.com',
            'password' => 'testtest',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'test_user1@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['email' => 'ログイン情報が存在しません。']);
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::where('email', 'test_user1@example.com')->first();

        $this->actingAs($user);

        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }
}