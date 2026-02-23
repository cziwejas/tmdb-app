<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'tmdb_id',
        'title',
        'overview',
        'release_date',
    ];

    protected $casts = [
        'title' => 'array',
        'overview' => 'array',
    ];
}
