<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;



    //ログイン画面
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    //ログイン
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->post('/login', [
            'email' => 'test_user1@example.com',
            'password' => 'testtest',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'test_user1@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['email' => 'ログイン情報が存在しません。']);
    }

    public function test_users_can_logout(): void
    {
        $user = User::where('email', 'test_user1@example.com')->first();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_admin_user_can_authenticate(): void
    {
        $response = $this->post('/login', [
            'email' => 'test_admin@example.com',
            'password' => 'adminadmin',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin/owners');
    }

    public function test_owner_user_can_authenticate(): void
    {
        $response = $this->post('/login', [
            'email' => 'test_owner@example.com',
            'password' => 'ownerowner',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/owner');
    }

    public function test_login_with_nonexistent_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['email' => 'ログイン情報が存在しません。']);
    }

    public function test_user_role_methods(): void
    {
        $user = User::where('email', 'test_user1@example.com')->first();
        $admin = User::where('email', 'test_admin@example.com')->first();
        $owner = User::where('email', 'test_owner@example.com')->first();

        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isOwner());

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isUser());
        $this->assertFalse($admin->isOwner());

        $this->assertTrue($owner->isOwner());
        $this->assertFalse($owner->isUser());
        $this->assertFalse($owner->isAdmin());
    }
}
