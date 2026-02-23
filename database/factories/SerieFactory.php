<?php

namespace Database\Factories;

use App\Models\Serie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Serie>
 */
class SerieFactory extends Factory
{
    protected $model = Serie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tmdb_id' => fake()->unique()->numberBetween(1, 100000),
            'name' => [
                'en' => fake()->sentence(3),
                'pl' => fake()->sentence(3),
            ],
            'overview' => [
                'en' => fake()->paragraph(),
                'pl' => fake()->paragraph(),
            ],
            'first_air_date' => fake()->date(),
        ];
    }
}
