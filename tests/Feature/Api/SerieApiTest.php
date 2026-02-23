<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Serie;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SerieApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_paginated_series_list()
    {
        // Arrange
        Serie::factory()->count(12)->create();

        // Act
        $response = $this->getJson('/api/series');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'tmdb_id', 'name', 'overview', 'first_air_date']
                ]
            ])
            ->assertJsonCount(10, 'data');
    }

    /** @test */
    public function it_returns_series_in_requested_locale()
    {
        // Arrange
        Serie::create([
            'tmdb_id' => 67890,
            'name' => [
                'en' => 'Breaking Bad',
                'pl' => 'Breaking Bad (PL)',
                'de' => 'Breaking Bad (DE)'
            ],
            'overview' => [
                'en' => 'A chemistry teacher...',
                'pl' => 'Nauczyciel chemii...',
                'de' => 'Ein Chemielehrer...'
            ],
            'first_air_date' => '2008-01-20'
        ]);

        // Act
        $response = $this->withHeaders([
            'Accept-Language' => 'de'
        ])->getJson('/api/series');

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Breaking Bad (DE)',
                'overview' => 'Ein Chemielehrer...'
            ]);
    }
}
