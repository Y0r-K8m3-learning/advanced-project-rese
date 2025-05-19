<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Genre;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('genres')->truncate();
        $genres = [
            ['name' => '居酒屋'],
            ['name' =>
            'イタリアン'],
            ['name' =>
            'ラーメン'],
            ['name' => '焼肉'],
            ['name' => '寿司'],
        ];

        foreach ($genres as $genre) {
            Genre::create($genre); // Eloquentのcreateメソッドを使用
        }
    }
}
