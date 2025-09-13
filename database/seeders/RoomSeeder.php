<?php

namespace Database\Seeders;

use DB;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB as FacadesDB;

class RoomSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $rooms = [
            [
                'name' => '1',
                'name_uz' => '1',
                'capacity' => 10,
                'daily_rate' => 150000, // Kunlik narx - 200,000 so'm (eski: 25,000/soat)
                'description' => 'Cozy room for small groups',
                'amenities' => ['TV', 'AC', 'WiFi'],
                'status' => 'available',
            ],
            [
                'name' => '2',
                'name_uz' => '2',
                'capacity' => 15,
                'daily_rate' => 200000, // Kunlik narx - 200,000 so'm
                'description' => 'Comfortable small room',
                'amenities' => ['TV', 'AC'],
                'status' => 'available',
            ],
            [
                'name' => '3',
                'name_uz' => '3',
                'capacity' => 20,
                'daily_rate' => 250000, // Kunlik narx - 350,000 so'm (eski: 40,000/soat)
                'description' => 'Perfect for medium groups',
                'amenities' => ['TV', 'AC', 'WiFi', 'Sound System'],
                'status' => 'available',
            ],
            [
                'name' => '4',
                'name_uz' => '4',
                'capacity' => 20,
                'daily_rate' => 250000, // Kunlik narx - 350,000 so'm
                'description' => 'Spacious medium room',
                'amenities' => ['TV', 'AC', 'WiFi'],
                'status' => 'available',
            ],
            [
                'name' => '5',
                'name_uz' => '5',
                'capacity' => 10,
                'daily_rate' => 150000, // Kunlik narx - 500,000 so'm (eski: 60,000/soat)
                'description' => 'Large room for celebrations',
                'amenities' => ['Large TV', 'AC', 'WiFi', 'Sound System', 'Microphone'],
                'status' => 'available',
            ],
            [
                'name' => '6',
                'name_uz' => '6',
                'capacity' => 10,
                'daily_rate' => 150000, // Kunlik narx - 700,000 so'm (eski: 80,000/soat)
                'description' => 'Luxury VIP room with premium amenities',
                'amenities' => ['Smart TV', 'Premium AC', 'WiFi', 'Sound System', 'Mini Bar', 'Private Bathroom'],
                'status' => 'available',
            ],
            [
                'name' => '7',
                'name_uz' => '7',
                'capacity' => 10,
                'daily_rate' => 150000, // Kunlik narx - 400,000 so'm (eski: 50,000/soat)
                'description' => 'Family-friendly room',
                'amenities' => ['TV', 'AC', 'WiFi', 'Kids Area'],
                'status' => 'available',
            ],
            [
                'name' => '8',
                'name_uz' => '8',
                'capacity' => 10,
                'daily_rate' => 150000, // Kunlik narx - 380,000 so'm (eski: 45,000/soat)
                'description' => 'Professional meeting room',
                'amenities' => ['Projector', 'Whiteboard', 'AC', 'WiFi', 'Conference Phone'],
                'status' => 'available',
            ],
        ];

        // Eski ma'lumotlarni xavfsiz o'chirish
        // Foreign key constraint tufayli truncate ishlamaydi
        FacadesDB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Room::truncate();
        FacadesDB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Yoki oddiy delete ishlatish:
        // Room::query()->delete();

        // Yangi ma'lumotlarni qo'shish
        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
