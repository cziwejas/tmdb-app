<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tmdb_id' => fake()->unique()->numberBetween(1, 100000),
            'title' => [
                'en' => fake()->sentence(3),
                'pl' => fake()->sentence(3),
            ],
            'overview' => [
                'en' => fake()->paragraph(),
                'pl' => fake()->paragraph(),
            ],
            'release_date' => fake()->date(),
        ];
    }
}
