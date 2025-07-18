<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Kamar Tipe Standart (ID: 1) - Stok: 5
        $standartRooms = ['101', '102', '103', '104', '105'];
        foreach ($standartRooms as $roomNumber) {
            DB::table('rooms')->insert([
                'room_number' => $roomNumber,
                'room_type_id' => 1,
                'bed_type' => 'Double',
                'number_of_bed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Kamar Tipe Deluxe (ID: 2) - Stok: 5
        $deluxeRooms = ['201', '202', '203', '204', '205'];
        foreach ($deluxeRooms as $roomNumber) {
            DB::table('rooms')->insert([
                'room_number' => $roomNumber,
                'room_type_id' => 2,
                'bed_type' => 'Double',
                'number_of_bed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Kamar Tipe Executive (ID: 3) - Stok: 4
        $executiveRooms = ['301', '302', '303', '304'];
        foreach ($executiveRooms as $roomNumber) {
            DB::table('rooms')->insert([
                'room_number' => $roomNumber,
                'room_type_id' => 3,
                'bed_type' => 'Double',
                'number_of_bed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
