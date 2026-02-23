<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Lista Filmów</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($movies as $movie)
            <div class="bg-white border border-gray-200 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="p-5">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2 line-clamp-2">
                        {{ $movie->title[app()->getLocale()] ?? $movie->title['en'] ?? 'Brak tytułu' }}
                    </h2>

                    @if($movie->release_date)
                        <p class="text-sm text-gray-500 mb-3">
                            <span class="font-medium">Data premiery:</span> 
                            {{ \Carbon\Carbon::parse($movie->release_date)->format('d.m.Y') }}
                        </p>
                    @endif

                    <p class="text-sm text-gray-700 line-clamp-3">
                        {{ $movie->overview[app()->getLocale()] ?? $movie->overview['en'] ?? 'Brak opisu' }}
                    </p>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                            TMDB ID: {{ $movie->tmdb_id }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">Brak filmów do wyświetlenia.</p>
                <p class="text-gray-400 text-sm mt-2">Uruchom komendę: php artisan tmdb:fetch</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $movies->links() }}
    </div>
</div>
