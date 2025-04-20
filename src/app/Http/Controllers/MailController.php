<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UserNotification;

class MailController extends Controller
{

    public function sendMailToAll(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $users = User::where('role_id', User::ROLE_USER)->get();
        $users = User::where('role_id', User::ROLE_USER)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new UserNotification($validated['subject'], $validated['message']));
        }

        return redirect()->back()->with('success', 'メールを送信完了');
    }

    public function create()
    {
        $users = User::where('role_id', User::ROLE_USER)->get();
        return view('admin.mail.create', compact('users'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required',
            'message' => 'required',
        ]);

        $users = User::where('role_id', User::ROLE_USER)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new UserNotification($validated['subject'], $validated['message']));
        }

        return redirect()->route('admin.mail.create')->with('success', 'メールを全ての利用者に送信しました');
    }
}
