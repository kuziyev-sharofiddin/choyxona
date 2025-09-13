<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $hotDrinks = Category::where('name_uz', 'Issiq Ichimliklar')->first();
        $coldDrinks = Category::where('name_uz', 'Sovuq Ichimliklar')->first();
        $mainDishes = Category::where('name_uz', 'Asosiy Taomlar')->first();
        $appetizers = Category::where('name_uz', 'Salat va Gazaklar')->first();
        $desserts = Category::where('name_uz', 'Shirinliklar')->first();
        $bread = Category::where('name_uz', 'Non va Xamir Ishi')->first();

        $products = [
            // Hot Drinks
            [
                'name' => 'Green Tea',
                'name_uz' => 'Ko\'k Choy',
                'description' => 'Traditional Uzbek green tea',
                'description_uz' => 'An\'anaviy O\'zbek kok choy',
                'price' => 8000,
                'category_id' => $hotDrinks->id,
                'preparation_time' => 5,
                'is_popular' => true,
            ],
            [
                'name' => 'Black Tea',
                'name_uz' => 'Qora Choy',
                'description' => 'Strong black tea with sugar',
                'description_uz' => 'Shakar bilan kuchli qora choy',
                'price' => 8000,
                'category_id' => $hotDrinks->id,
                'preparation_time' => 5,
            ],
            [
                'name' => 'Coffee',
                'name_uz' => 'Qahva',
                'description' => 'Fresh brewed coffee',
                'description_uz' => 'Yangi qaynatilgan qahva',
                'price' => 15000,
                'category_id' => $hotDrinks->id,
                'preparation_time' => 7,
            ],

            // Cold Drinks
            [
                'name' => 'Ayran',
                'name_uz' => 'Ayron',
                'description' => 'Traditional yogurt drink',
                'description_uz' => 'An\'anaviy yogurt ichimlik',
                'price' => 12000,
                'category_id' => $coldDrinks->id,
                'preparation_time' => 3,
                'is_popular' => true,
            ],
            [
                'name' => 'Fresh Juice',
                'name_uz' => 'Toza Meva Sharbati',
                'description' => 'Seasonal fruit juice',
                'description_uz' => 'Mavsumiy meva sharbati',
                'price' => 18000,
                'category_id' => $coldDrinks->id,
                'preparation_time' => 5,
            ],

            // Main Dishes
            [
                'name' => 'Plov',
                'name_uz' => 'Osh',
                'description' => 'Traditional Uzbek pilaf with lamb',
                'description_uz' => 'Qo\'zi go\'shti bilan an\'anaviy O\'zbek oshi',
                'price' => 35000,
                'category_id' => $mainDishes->id,
                'preparation_time' => 25,
                'is_popular' => true,
                'ingredients' => ['rice', 'lamb', 'carrot', 'onion', 'oil'],
            ],
            [
                'name' => 'Shashlik',
                'name_uz' => 'Shashlik',
                'description' => 'Grilled meat skewers',
                'description_uz' => 'Panjara ustida pishirilgan go\'sht',
                'price' => 45000,
                'category_id' => $mainDishes->id,
                'preparation_time' => 20,
                'is_popular' => true,
                'ingredients' => ['lamb', 'beef', 'onion', 'spices'],
            ],
            [
                'name' => 'Manti',
                'name_uz' => 'Manti',
                'description' => 'Steamed dumplings with meat',
                'description_uz' => 'Go\'sht bilan bug\'da pishirilgan chuchvara',
                'price' => 28000,
                'category_id' => $mainDishes->id,
                'preparation_time' => 30,
                'ingredients' => ['flour', 'lamb', 'onion', 'pumpkin'],
            ],
            [
                'name' => 'Lagman',
                'name_uz' => 'Lag\'mon',
                'description' => 'Hand-pulled noodle soup',
                'description_uz' => 'Qo\'lda tortilgan noodle sho\'rva',
                'price' => 25000,
                'category_id' => $mainDishes->id,
                'preparation_time' => 20,
                'ingredients' => ['noodles', 'vegetables', 'meat', 'broth'],
            ],

            // Appetizers
            [
                'name' => 'Achichuk Salad',
                'name_uz' => 'Achichuk Salat',
                'description' => 'Fresh tomato and onion salad',
                'description_uz' => 'Yangi pomidor va piyoz salati',
                'price' => 15000,
                'category_id' => $appetizers->id,
                'preparation_time' => 5,
                'ingredients' => ['tomato', 'onion', 'herbs'],
            ],
            [
                'name' => 'Somsa',
                'name_uz' => 'Somsa',
                'description' => 'Baked meat pastry',
                'description_uz' => 'Go\'sht bilan pishirilgan xamir ishi',
                'price' => 8000,
                'category_id' => $appetizers->id,
                'preparation_time' => 15,
                'is_popular' => true,
                'ingredients' => ['flour', 'lamb', 'onion'],
            ],
            [
                'name' => 'Cheese Plate',
                'name_uz' => 'Pishloq Tarelkasi',
                'description' => 'Local cheese selection',
                'description_uz' => 'Mahalliy pishloqlar tanlov',
                'price' => 22000,
                'category_id' => $appetizers->id,
                'preparation_time' => 5,
            ],

            // Desserts
            [
                'name' => 'Halva',
                'name_uz' => 'Holva',
                'description' => 'Traditional sweet dessert',
                'description_uz' => 'An\'anaviy shirin desert',
                'price' => 12000,
                'category_id' => $desserts->id,
                'preparation_time' => 3,
            ],
            [
                'name' => 'Chak-chak',
                'name_uz' => 'Chak-chak',
                'description' => 'Honey-coated fried dough',
                'description_uz' => 'Asal bilan qoplangan qovurilgan xamir',
                'price' => 15000,
                'category_id' => $desserts->id,
                'preparation_time' => 5,
                'is_popular' => true,
            ],

            // Bread
            [
                'name' => 'Uzbek Bread',
                'name_uz' => 'O\'zbek Noni',
                'description' => 'Traditional round flatbread',
                'description_uz' => 'An\'anaviy dumaloq lepyoshka',
                'price' => 3000,
                'category_id' => $bread->id,
                'preparation_time' => 10,
                'is_popular' => true,
            ],
            [
                'name' => 'Patyr',
                'name_uz' => 'Patir',
                'description' => 'Layered bread with oil',
                'description_uz' => 'Yog\' bilan qatlamli non',
                'price' => 4000,
                'category_id' => $bread->id,
                'preparation_time' => 12,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
