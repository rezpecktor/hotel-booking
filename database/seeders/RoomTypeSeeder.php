<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('room_types')->insert([
            ['name' => 'Standart', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deluxe', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Executive', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
