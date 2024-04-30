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
            'title' => fake()->jobTitle(),
            'description' => fake()->realText($maxNbChars = 200, $indexSize = 2),
            'location' => fake()->state(),
            'pay' => rand(100000,500000),
            'company_id' => 1,
            'is_active' => 1,
            'is_trending' => rand(0,1),
        ];
    }
}
