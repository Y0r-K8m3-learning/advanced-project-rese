<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        // if ($user === null) {
        //     // ユーザーが認証されていない場合、ログインページにリダイレクト
        //     return redirect()->route('login')->withErrors(['message' => 'ログインが必要です']);
        // }

        // if ($user->hasVerifiedEmail()) {
        //     return redirect()->route('registration.complete'); // 既に確認済みなら完了画面へ
        // }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }


        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
    }
}
