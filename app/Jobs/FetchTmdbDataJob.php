<?php

namespace App\Jobs;

use App\Services\TmdbService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchTmdbDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Liczba prób wykonania zadania.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Czas oczekiwania między próbami (w sekundach).
     *
     * @var array
     */
    public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min

    /**
     * Maksymalny czas wykonania zadania (w sekundach).
     *
     * @var int
     */
    public $timeout = 600; // 10 minut

    /**
     * Wykonaj zadanie.
     */
    public function handle(TmdbService $tmdbService): void
    {
        try {
            Log::info('Starting TMDB data fetch job');

            // Pobierz gatunki
            Log::info('Fetching genres from TMDB');
            $tmdbService->fetchGenres();

            // Pobierz filmy
            Log::info('Fetching 50 movies from TMDB');
            $tmdbService->fetchMovies(50);

            // Pobierz seriale
            Log::info('Fetching 10 series from TMDB');
            $tmdbService->fetchSeries(10);

            Log::info('TMDB data fetch job completed successfully');
        } catch (Throwable $exception) {
            Log::error('Error during TMDB data fetch', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'attempt' => $this->attempts(),
            ]);

            // Rzuć wyjątek ponownie, aby Queue mógł obsłużyć retry
            throw $exception;
        }
    }

    /**
     * Obsługa niepowodzenia zadania.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('TMDB data fetch job failed after all retries', [
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'attempts' => $this->attempts(),
        ]);

        // Tutaj można dodać powiadomienie dla administratora
        // np. wysłanie emaila lub notyfikacji Slack
    }
}