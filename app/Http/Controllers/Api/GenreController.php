<?php

namespace App\Http\Controllers\Api;

use App\Models\Genre;
use App\Http\Resources\GenreResource;
use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function __invoke()
    {
        return GenreResource::collection(
            Genre::paginate(10)
        );
    }
}
