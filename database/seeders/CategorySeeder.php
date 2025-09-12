<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Hot Drinks',
                'name_uz' => 'Issiq Ichimliklar',
                'description' => 'Traditional hot beverages',
                'sort_order' => 1,
            ],
            [
                'name' => 'Cold Drinks',
                'name_uz' => 'Sovuq Ichimliklar',
                'description' => 'Refreshing cold beverages',
                'sort_order' => 2,
            ],
            [
                'name' => 'Main Dishes',
                'name_uz' => 'Asosiy Taomlar',
                'description' => 'Traditional Uzbek main courses',
                'sort_order' => 3,
            ],
            [
                'name' => 'Appetizers',
                'name_uz' => 'Salat va Gazaklar',
                'description' => 'Light meals and appetizers',
                'sort_order' => 4,
            ],
            [
                'name' => 'Desserts',
                'name_uz' => 'Shirinliklar',
                'description' => 'Traditional sweets and desserts',
                'sort_order' => 5,
            ],
            [
                'name' => 'Bread',
                'name_uz' => 'Non va Xamir Ishi',
                'description' => 'Fresh bread and pastries',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
