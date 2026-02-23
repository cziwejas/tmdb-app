<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_genres_list()
    {
        // Arrange
        Genre::factory()->count(5)->create();

        // Act
        $response = $this->getJson('/api/genres');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'tmdb_id', 'name']
                ]
            ])
            ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function it_returns_genre_names_in_correct_locale()
    {
        // Arrange
        Genre::create([
            'tmdb_id' => 28,
            'name' => [
                'en' => 'Action',
                'pl' => 'Akcja',
                'de' => 'Action'
            ]
        ]);

        // Act
        $response = $this->withHeaders([
            'Accept-Language' => 'pl'
        ])->getJson('/api/genres');

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Akcja']);
    }
}
