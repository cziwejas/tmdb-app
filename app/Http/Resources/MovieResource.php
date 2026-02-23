<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'tmdb_id' => $this->tmdb_id,
            'title' => $this->title[$locale] ?? $this->title['en'] ?? null,
            'overview' => $this->overview[$locale] ?? $this->overview['en'] ?? null,
            'release_date' => $this->release_date,
        ];
    }
}