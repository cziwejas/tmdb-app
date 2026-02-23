<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MovieApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_paginated_movies_list()
    {
        // Arrange: Tworzymy 15 filmów
        Movie::factory()->count(15)->create();

        // Act: Wywołujemy endpoint
        $response = $this->getJson('/api/movies');

        // Assert: Sprawdzamy strukturę odpowiedzi
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'tmdb_id', 'title', 'overview', 'release_date']
                ],
                'links',
                'meta'
            ])
            ->assertJsonCount(10, 'data'); // Domyślna paginacja: 10
    }

    /** @test */
    public function it_returns_movies_in_correct_locale()
    {
        // Arrange: Film z tłumaczeniami
        Movie::create([
            'tmdb_id' => 12345,
            'title' => [
                'en' => 'English Title',
                'pl' => 'Polski Tytuł',
                'de' => 'Deutscher Titel'
            ],
            'overview' => [
                'en' => 'English overview',
                'pl' => 'Polski opis',
                'de' => 'Deutsche Beschreibung'
            ],
            'release_date' => '2024-01-01'
        ]);

        // Act: Zapytanie z nagłówkiem Accept-Language: pl
        $response = $this->withHeaders([
            'Accept-Language' => 'pl-PL,pl;q=0.9'
        ])->getJson('/api/movies');

        // Assert: Zwraca polską wersję
        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Polski Tytuł',
                'overview' => 'Polski opis'
            ]);
    }

    /** @test */
    public function it_falls_back_to_english_when_locale_not_available()
    {
        // Arrange: Film tylko z angielskim
        Movie::create([
            'tmdb_id' => 12345,
            'title' => ['en' => 'English Title'],
            'overview' => ['en' => 'English overview'],
            'release_date' => '2024-01-01'
        ]);

        // Act: Zapytanie z nieobsługiwanym językiem
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR'
        ])->getJson('/api/movies');

        // Assert: Fallback do angielskiego
        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'English Title'
            ]);
    }
}
