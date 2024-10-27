<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 一般ユーザー
        User::create([
            'name' => 'Test User',
            'email' => 'test_user@example.com',
            'password' => Hash::make('testtest'),
            'role_id' =>  User::ROLE_USER,
        ]);

        // オーナー
        User::create([
            'name' => 'Test Owner',
            'email' => 'test_owner@example.com',
            'password' => Hash::make('ownerowner'),
            'role_id' => User::ROLE_OWNER,
            'email_verified_at' => Carbon::now(),
        ]);

        // 管理者
        User::create([
            'name' => 'Test Admin',
            'email' => 'test_admin@example.com',
            'password' => Hash::make('adminadmin'),  // パスワードをハッシュ化
            'role_id' => User::ROLE_ADMIN,
            'email_verified_at' => Carbon::now(),
        ]);
    }
}
