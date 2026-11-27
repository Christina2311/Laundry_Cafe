<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Utilities',
                'description' => 'Electricity, water, and other utility bills.',
            ],
            [
                'category_name' => 'Rent',
                'description' => 'Rental expenses for the business premises.',
            ],
            [
                'category_name' => 'Salaries and Wages',
                'description' => 'Employee salaries, wages, and compensation.',
            ],
            [
                'category_name' => 'Maintenance and Repairs',
                'description' => 'Equipment and facility maintenance and repairs.',
            ],
            [
                'category_name' => 'Internet and Communication',
                'description' => 'Internet subscriptions and communication expenses.',
            ],
            [
                'category_name' => 'Transportation',
                'description' => 'Transportation, delivery, and travel expenses.',
            ],
            [
                'category_name' => 'Marketing',
                'description' => 'Advertising, promotions, and marketing expenses.',
            ],
            [
                'category_name' => 'Permits and Licenses',
                'description' => 'Business permits, licenses, and registration fees.',
            ],
            [
                'category_name' => 'Miscellaneous',
                'description' => 'Other operational expenses.',
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(
                ['category_name' => $category['category_name']],
                ['description' => $category['description']]
            );
        }
    }
}
