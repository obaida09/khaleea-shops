<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(10)),
            'discount' => $this->faker->randomFloat(2, 5, 50),
            'discount_type' => $this->faker->randomElement(['fixed', 'percentage']),
            'usage_limit' => $this->faker->optional()->numberBetween(1, 100),
            'used' => 5,
            'valid_from' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'valid_until' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
