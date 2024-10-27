<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }



    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->validated();

        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // メールアドレスが確認済みかチェック
        if (!$user->hasVerifiedEmail()) {

            $user->sendEmailVerificationNotification();

            Auth::logout();

            return redirect('/login')->with('error', 'メールアドレスが認証されていません。' . "\n" . '確認リンクをメールで再送信しました。');
        }


        $redirectUrl = session('redirect_url', route('home', absolute: false));
        $request->session()->forget('redirect_url'); 

        // ロールに応じてリダイレクト
        if ($user->isAdmin()) {
            return redirect()->route('admin.owners.index'); 
        } elseif ($user->isOwner()) {
            return redirect()->route('owner'); 
        }


        return redirect()->intended($redirectUrl);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
