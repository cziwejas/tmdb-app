<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\SerieController;
use App\Http\Controllers\Api\GenreController;

Route::get('/movies', MovieController::class);
Route::get('/series', SerieController::class);
Route::get('/genres', GenreController::class);