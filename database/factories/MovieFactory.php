<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genres = ['Action', 'Comedy', 'Drama', 'Thriller'];
        return [
            'title' => fake()->country(),
            'genre' => json_encode($genres[array_rand($genres)]),
            'plot' => fake()->paragraph(),
            'runtime' => fake()->numberBetween(100, 250),
            'released_in' => rand(1990, 2024),
            'production_id' => rand(1, 10),
        ];
    }
}
