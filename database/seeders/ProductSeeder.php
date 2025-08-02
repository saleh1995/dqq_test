<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            'Laptop',
            'Smartphone',
            'Tablet',
            'Headphones',
            'Keyboard',
            'Mouse',
            'Monitor',
            'Printer',
            'Scanner',
            'Webcam',
        ];

        foreach ($products as $productName) {
            Product::updateOrCreate(
                ['name' => $productName],
                ['name' => $productName]
            );
        }
    }
}
