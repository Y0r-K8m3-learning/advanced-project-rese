<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        dd(1);
        // ログインせずにメール再送信画面を表示
        if ($request->user() === null) {
            return view('auth.verify-email');  // ログインしていない場合の処理
        }

        // メール確認済みならダッシュボードへリダイレクト
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(route('dashboard', absolute: false))
            : view('auth.verify-email');

        // return $request->user()->hasVerifiedEmail()
        //     ? redirect()->intended(route('dashboard', absolute: false))
        //     : view('auth.verify-email');
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(route('dashboard', absolute: false))
            : view('auth.verify-email');
    }
}
