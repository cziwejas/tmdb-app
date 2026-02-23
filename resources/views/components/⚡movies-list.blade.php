<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="max-w-4xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">Movies</h1>

    <div class="space-y-4">
        @foreach ($movies as $movie)
            <div class="border p-4 rounded shadow-sm">
                <h2 class="text-lg font-semibold">
                    {{ $movie->title[app()->getLocale()] ?? $movie->title['en'] ?? '—' }}
                </h2>

                <p class="text-sm text-gray-600">
                    {{ $movie->release_date }}
                </p>

                <p class="mt-2">
                    {{ $movie->overview[app()->getLocale()] ?? $movie->overview['en'] ?? '' }}
                </p>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $movies->links() }}
    </div>

</div>