<?php

namespace Database\Seeders;

use App\Models\MeasurementUnit;
use Illuminate\Database\Seeder;

class MeasurementUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['unit_name' => 'Gram', 'abbreviation' => 'g'],
            ['unit_name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['unit_name' => 'Milliliter', 'abbreviation' => 'mL'],
            ['unit_name' => 'Liter', 'abbreviation' => 'L'],
            ['unit_name' => 'Piece', 'abbreviation' => 'pcs'],
            ['unit_name' => 'Pack', 'abbreviation' => 'pack'],
            ['unit_name' => 'Box', 'abbreviation' => 'box'],
            ['unit_name' => 'Bottle', 'abbreviation' => 'btl'],
            ['unit_name' => 'Sachet', 'abbreviation' => 'sachet'],
            ['unit_name' => 'Bag', 'abbreviation' => 'bag'],
            ['unit_name' => 'Roll', 'abbreviation' => 'roll'],
            ['unit_name' => 'Gallon', 'abbreviation' => 'gal'],
        ];

        foreach ($units as $unit) {
            MeasurementUnit::firstOrCreate(
                ['abbreviation' => $unit['abbreviation']],
                ['unit_name' => $unit['unit_name']]
            );
        }
    }
}
