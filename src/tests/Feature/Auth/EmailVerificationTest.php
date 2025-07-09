<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use DatabaseTransactions;
    private string $usermail ='test_user1@example.com';
   
    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::where('email', $this->usermail)->first();
        $user->save();

        $response = $this->actingAs($user)->get('/verify-email');
        $response->assertRedirect(route('home'));
       
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::where('email', $this->usermail)->first();
        $user->email_verified_at = null;
        $user->save();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect('/?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::where('email', $this->usermail)->first();
        $user->email_verified_at = null;
        $user->save();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
