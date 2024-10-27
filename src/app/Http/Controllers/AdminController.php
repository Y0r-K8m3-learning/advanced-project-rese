<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // オーナー一覧画面
    public function index()
    {
        $owners = User::where('role_id', User::ROLE_OWNER)->get();
        $users = User::where('role_id', User::ROLE_USER)->get();
        return view('admin.owners.index', compact('owners', 'users'));
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => User::ROLE_OWNER,
        ]);


        return redirect()->route('admin.owners.index')->with('success', 'オーナーが登録されました');
    }
}
