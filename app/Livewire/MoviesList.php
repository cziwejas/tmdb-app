<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Movie;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Lista Filmów')]
#[Layout('layouts.app')]
class MoviesList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $perPage = 12;

    public function render()
    {
        $movies = Movie::orderByDesc('release_date')
            ->paginate($this->perPage);

        return view('livewire.movies-list', [
            'movies' => $movies,
        ]);
    }
}