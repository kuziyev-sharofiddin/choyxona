<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $waiterRole = Role::where('name', 'waiter')->first();
        $kitchenRole = Role::where('name', 'kitchen')->first();
        $cashierRole = Role::where('name', 'cashier')->first();

        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@choyxona.uz',
            'phone' => '+998901234567',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
            'salary' => 5000000,
            'hire_date' => now()->subMonths(6),
        ]);

        // Sample waiters
        $waiters = [
            ['name' => 'Akmal Karimov', 'email' => 'akmal@choyxona.uz', 'phone' => '+998901234568'],
            ['name' => 'Dilshoda Umarova', 'email' => 'dilshoda@choyxona.uz', 'phone' => '+998901234569'],
            ['name' => 'Bobur Toshmatov', 'email' => 'bobur@choyxona.uz', 'phone' => '+998901234570'],
        ];

        foreach ($waiters as $waiter) {
            User::create([
                'name' => $waiter['name'],
                'email' => $waiter['email'],
                'phone' => $waiter['phone'],
                'password' => Hash::make('waiter123'),
                'role_id' => $waiterRole->id,
                'salary' => 3000000,
                'hire_date' => now()->subMonths(rand(1, 12)),
            ]);
        }

        // Kitchen staff
        User::create([
            'name' => 'Oybek Oshpaz',
            'email' => 'oybek@choyxona.uz',
            'phone' => '+998901234571',
            'password' => Hash::make('kitchen123'),
            'role_id' => $kitchenRole->id,
            'salary' => 4000000,
            'hire_date' => now()->subMonths(8),
        ]);

        // Cashier
        User::create([
            'name' => 'Maryam Kasirova',
            'email' => 'maryam@choyxona.uz',
            'phone' => '+998901234572',
            'password' => Hash::make('cashier123'),
            'role_id' => $cashierRole->id,
            'salary' => 3500000,
            'hire_date' => now()->subMonths(4),
        ]);
    }
}
