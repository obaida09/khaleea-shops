<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory(1000)->create()->each(function ($product) {
            // Attach 3 random images to each product
            ProductImage::factory()->count(3)->create([
                'product_id' => $product->id, // Associate with the created product
            ]);
        });
    }
}
