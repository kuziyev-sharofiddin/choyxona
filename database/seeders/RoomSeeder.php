<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $rooms = [
            [
                'name' => 'Small Room 1',
                'name_uz' => 'Kichik Xona 1',
                'capacity' => 4,
                'hourly_rate' => 25000,
                'description' => 'Cozy room for small groups',
                'amenities' => ['TV', 'AC', 'WiFi'],
                'status' => 'available',
            ],
            [
                'name' => 'Small Room 2',
                'name_uz' => 'Kichik Xona 2',
                'capacity' => 4,
                'hourly_rate' => 25000,
                'description' => 'Comfortable small room',
                'amenities' => ['TV', 'AC'],
                'status' => 'available',
            ],
            [
                'name' => 'Medium Room 1',
                'name_uz' => 'O\'rta Xona 1',
                'capacity' => 8,
                'hourly_rate' => 40000,
                'description' => 'Perfect for medium groups',
                'amenities' => ['TV', 'AC', 'WiFi', 'Sound System'],
                'status' => 'available',
            ],
            [
                'name' => 'Medium Room 2',
                'name_uz' => 'O\'rta Xona 2',
                'capacity' => 8,
                'hourly_rate' => 40000,
                'description' => 'Spacious medium room',
                'amenities' => ['TV', 'AC', 'WiFi'],
                'status' => 'available',
            ],
            [
                'name' => 'Large Room 1',
                'name_uz' => 'Katta Xona 1',
                'capacity' => 15,
                'hourly_rate' => 60000,
                'description' => 'Large room for celebrations',
                'amenities' => ['Large TV', 'AC', 'WiFi', 'Sound System', 'Microphone'],
                'status' => 'available',
            ],
            [
                'name' => 'VIP Room',
                'name_uz' => 'VIP Xona',
                'capacity' => 12,
                'hourly_rate' => 80000,
                'description' => 'Luxury VIP room with premium amenities',
                'amenities' => ['Smart TV', 'Premium AC', 'WiFi', 'Sound System', 'Mini Bar', 'Private Bathroom'],
                'status' => 'available',
            ],
            [
                'name' => 'Family Room',
                'name_uz' => 'Oilaviy Xona',
                'capacity' => 10,
                'hourly_rate' => 50000,
                'description' => 'Family-friendly room',
                'amenities' => ['TV', 'AC', 'WiFi', 'Kids Area'],
                'status' => 'available',
            ],
            [
                'name' => 'Business Room',
                'name_uz' => 'Biznes Xona',
                'capacity' => 6,
                'hourly_rate' => 45000,
                'description' => 'Professional meeting room',
                'amenities' => ['Projector', 'Whiteboard', 'AC', 'WiFi', 'Conference Phone'],
                'status' => 'available',
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
