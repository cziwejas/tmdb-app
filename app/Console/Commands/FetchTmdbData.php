<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchTmdbDataJob;

class FetchTmdbData extends Command
{
    protected $signature = 'tmdb:fetch';
    protected $description = 'Dispatch job to fetch data from TMDB';

    public function handle(): int
    {
        FetchTmdbDataJob::dispatch();

        $this->info('TMDB fetch job dispatched.');

        return Command::SUCCESS;
    }
}