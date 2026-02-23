<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

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
