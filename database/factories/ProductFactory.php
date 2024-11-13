<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true), // Custom method or string
            'shop_id' => shop::first()->id,
            'category_id' => Category::first()->id,
            'quantity' => rand(20,200),
            'season' => $this->faker->randomElement(['winter', 'all', 'summer']),
            'description' => $this->faker->text(200),
            'price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}

