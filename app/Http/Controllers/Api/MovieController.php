<?php

namespace App\Http\Controllers\Api;

use App\Models\Movie;
use App\Http\Controllers\Controller;
use App\Http\Resources\MovieResource;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function __invoke()
    {
        return MovieResource::collection(
            Movie::paginate(10)
        );
    }
}