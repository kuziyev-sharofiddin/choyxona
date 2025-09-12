<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'permissions' => [
                    'manage_users', 'manage_products', 'manage_rooms', 
                    'view_reports', 'manage_payments', 'manage_reservations'
                ]
            ],
            [
                'name' => 'waiter',
                'display_name' => 'Ofitsiant',
                'permissions' => [
                    'create_reservations', 'create_orders', 'view_orders', 'update_order_status'
                ]
            ],
            [
                'name' => 'kitchen',
                'display_name' => 'Oshpaz',
                'permissions' => [
                    'view_orders', 'update_order_status', 'manage_kitchen'
                ]
            ],
            [
                'name' => 'cashier',
                'display_name' => 'Kassa',
                'permissions' => [
                    'process_payments', 'view_payments', 'create_invoices'
                ]
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
