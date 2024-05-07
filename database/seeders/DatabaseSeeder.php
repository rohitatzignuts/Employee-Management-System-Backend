<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Actor;
use App\Models\Movie;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([CompaniesSeeder::class, UserSeeder::class, JobSeeder::class, ProductionSeeder::class, ActorSeeder::class, MovieSeeder::class]);

        $actors = Actor::all();
        $movies = Movie::all();

        // Attach actors to movies
        foreach ($movies as $movie) {
            $actorsIds = $actors->random(rand(1, 3))->pluck('id')->toArray();
            $movie->actors()->attach($actorsIds);
        }
    }
}
