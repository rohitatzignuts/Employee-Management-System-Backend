<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name(),
            'description' => fake()->text(),
            'location' => fake()->address(),
            'pay' => rand(100000,500000),
            'company_id' => rand(1,5),
            'is_active' => rand(0,1),
            'is_trending' => rand(0,1),
        ];
    }
}
