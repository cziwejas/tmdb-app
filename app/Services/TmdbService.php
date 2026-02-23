<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Serie;
use App\Models\Genre;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TmdbService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url');
        $this->token = config('services.tmdb.token');

        $this->validateConfiguration();
    }

    /**
     * Walidacja konfiguracji TMDB.
     *
     * @throws Exception
     */
    protected function validateConfiguration(): void
    {
        if (empty($this->token)) {
            throw new Exception(
                'TMDB token is not configured. Please set TMDB_TOKEN in your .env file.'
            );
        }

        if (empty($this->baseUrl)) {
            throw new Exception(
                'TMDB base URL is not configured. Please check your services.php config file.'
            );
        }

        Log::info('TMDB Service initialized', [
            'base_url' => $this->baseUrl,
            'token_configured' => !empty($this->token),
        ]);
    }

    protected function client()
    {
        return Http::withToken($this->token)
            ->baseUrl($this->baseUrl);
    }

    public function fetchGenres(): void
    {
        foreach (['en', 'pl', 'de'] as $locale) {
            $response = $this->client()
                ->get("/genre/movie/list", [
                    'language' => $locale
                ])
                ->throw()
                ->json();
            

            foreach ($response['genres'] as $genreData) {
                $genre = Genre::firstOrNew(
                    ['tmdb_id' => $genreData['id']] 
                );

                $name = $genre->name ?? [];
                $name[$locale] = $genreData['name'];

                $genre->name = $name;
                $genre->save();
            }
        }
    }

    public function fetchMovies(int $limit = 50): void
    {
        $this->fetchMedia('movie', Movie::class, $limit);
    }

    public function fetchSeries(int $limit = 10): void
    {
        $this->fetchMedia('tv', Serie::class, $limit);
    }

    protected function fetchMedia(string $type, string $model, int $limit): void
{
    $locales = ['en', 'pl', 'de'];
    $titleField = $type === 'movie' ? 'title' : 'name';
    $dateField = $type === 'movie' ? 'release_date' : 'first_air_date';

    $collected = [];
    $page = 1;
    $maxPages = 10; // Zabezpieczenie przed nieskończoną pętlą

    // Pobieramy kolejne strony aż uzyskamy wymagany limit
    while (count($collected) < $limit && $page <= $maxPages) {
        $response = $this->client()
            ->get("/discover/{$type}", [
                'language' => 'en',
                'sort_by' => 'popularity.desc',
                'page' => $page,
            ])
            ->throw()
            ->json();

        if (empty($response['results'])) {
            break; // brak kolejnych wyników
        }

        // Dodajemy wyniki z deduplikacją po tmdb_id
        foreach ($response['results'] as $item) {
            if (count($collected) >= $limit) {
                break 2; // Mamy już wystarczająco dużo unikalnych elementów
            }
            $collected[$item['id']] = $item;
        }
        
        $page++;
    }

    // Bierzemy tylko wymaganą liczbę unikalnych elementów
    $items = array_slice($collected, 0, $limit);

    foreach ($items as $item) {
        try {
            $record = $model::updateOrCreate(
                ['tmdb_id' => $item['id']],
                [
                    $titleField => [],
                    'overview' => [],
                    $dateField => $item[$dateField] ?? null,
                ]
            );

            foreach ($locales as $locale) {
                $translated = $this->client()
                    ->get("/{$type}/{$item['id']}", [
                        'language' => $locale
                    ])
                    ->throw()
                    ->json();

                $title = $record->{$titleField} ?? [];
                $overview = $record->overview ?? [];

                $title[$locale] = $translated[$titleField] ?? null;
                $overview[$locale] = $translated['overview'] ?? null;

                $record->update([
                    $titleField => $title,
                    'overview' => $overview,
                    $dateField => $translated[$dateField] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            // Logujemy błąd ale kontynuujemy z następnym elementem
            Log::error("Failed to fetch {$type} {$item['id']}: " . $e->getMessage(), [
                'type' => $type,
                'tmdb_id' => $item['id'],
                'exception' => $e->getMessage(),
            ]);
            continue;
        }
    }
}
}