<?php

namespace App\Http\Controllers\Api;

use App\Models\Serie;
use App\Http\Resources\SerieResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SerieController extends Controller
{
    public function __invoke()
    {
        return SerieResource::collection(
            Serie::paginate(10)
        );
    }
}
