<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 10, 'name' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'name' => 'owner', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
