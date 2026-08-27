<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        Customer::factory()
            ->count(10)
            ->create();

        $beverages = Category::create([
            'name' => 'Beverages',
        ]);

        $food = Category::create([
            'name' => 'Food',
        ]);

        $desserts = Category::create([
            'name' => 'Desserts',
        ]);

        Product::create([
            'category_id' => $beverages->id,
            'name' => 'Orange Juice',
            'description' => 'Fresh orange juice - 500ml',
            'price' => 8.50,
            'stock' => 30,
            'active' => true,
        ]);

        Product::create([
            'category_id' => $beverages->id,
            'name' => 'Mineral Water',
            'description' => 'Mineral water - 500ml',
            'price' => 4.00,
            'stock' => 50,
            'active' => true,
        ]);

        Product::create([
            'category_id' => $food->id,
            'name' => 'Classic Burger',
            'description' => 'Burger with beef, cheese and salad',
            'price' => 22.90,
            'stock' => 25,
            'active' => true,
        ]);

        Product::create([
            'category_id' => $food->id,
            'name' => 'French Fries',
            'description' => 'Crispy french fries',
            'price' => 12.50,
            'stock' => 40,
            'active' => true,
        ]);

        Product::create([
            'category_id' => $desserts->id,
            'name' => 'Chocolate Brownie',
            'description' => 'Chocolate brownie',
            'price' => 10.90,
            'stock' => 20,
            'active' => true,
        ]);
    }
}