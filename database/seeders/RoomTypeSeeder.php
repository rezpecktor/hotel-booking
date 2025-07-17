<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        RoomType::insert([
            ['name' => 'Standart', 'price' => 200000],
            ['name' => 'Deluxe', 'price' => 250000],
            ['name' => 'Executive', 'price' => 300000]
        ]);
    }
}
