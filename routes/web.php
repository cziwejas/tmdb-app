<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MovieController;
use App\Livewire\MoviesList;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/movies', MoviesList::class)->name('movies.index');