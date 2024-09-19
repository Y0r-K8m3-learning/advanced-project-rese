<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminConttoller extends Controller
{
    // オーナー一覧画面
    public function index()
    {
        // role_id が 20 のユーザーをオーナーとして扱う（例として）
        $owners = User::where('role_id', User::ROLE_OWNER)->get(); // オーナーの役割を role_id で識別


        // role_id が 10 のユーザは一般利用者
        $users = User::where('role_id', User::ROLE_USER)->get();

        return view('admin.owners.index', compact('owners', 'users'));
    }

    // オーナー情報を登録する処理
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
