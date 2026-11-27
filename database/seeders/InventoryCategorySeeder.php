<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use Illuminate\Database\Seeder;

class InventoryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Coffee Ingredients',
                'description' => 'Coffee beans and coffee-related ingredients.',
            ],
            [
                'category_name' => 'Beverage Ingredients',
                'description' => 'Milk, syrups, powders and other beverage ingredients.',
            ],
            [
                'category_name' => 'Food Ingredients',
                'description' => 'Ingredients used for food preparation.',
            ],
            [
                'category_name' => 'Laundry Supplies',
                'description' => 'Detergents, conditioners and laundry consumables.',
            ],
            [
                'category_name' => 'Cafe Packaging',
                'description' => 'Cups, lids, straws and takeaway packaging.',
            ],
            [
                'category_name' => 'Laundry Packaging',
                'description' => 'Laundry bags and packaging materials.',
            ],
            [
                'category_name' => 'Cleaning Supplies',
                'description' => 'General cleaning and sanitation supplies.',
            ],
            [
                'category_name' => 'Other Supplies',
                'description' => 'Other consumable inventory items.',
            ],
        ];

        foreach ($categories as $category) {
            InventoryCategory::firstOrCreate(
                ['category_name' => $category['category_name']],
                ['description' => $category['description']]
            );
        }
    }
}
