<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
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
            'name' => $this->faker->name(), // Custom method or string
            'slug' => $this->faker->randomFloat(2, 10, 1000), // Price between 10 and 1000
            'user_id' => User::first()->id,
            'category_id' => Category::first()->id,
            'description' => $this->faker->text(200),
            'price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
