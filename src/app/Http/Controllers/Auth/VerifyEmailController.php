<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {

      
        $user = User::findOrFail($request->route('id'));

        if (Auth::guest()) {
            $user = User::findOrFail($request->route('id'));
            Auth::login($user);
        }

        // メールアドレスがすでに確認されている場合
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/')->with('verified', true);
        }

        // 認証処理
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('home', absolute: false) . '?verified=1');
    }
}
