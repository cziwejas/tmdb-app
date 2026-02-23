<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    use HasFactory;

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
