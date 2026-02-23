<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $fillable = [
        'tmdb_id',
        'name',
        'overview',
        'first_air_date',
    ];

    protected $casts = [
        'name' => 'array',
        'overview' => 'array',
    ];
}
