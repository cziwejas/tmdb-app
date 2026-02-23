<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SerieResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'tmdb_id' => $this->tmdb_id,
            'name' => $this->name[$locale] ?? $this->name['en'] ?? null,
            'overview' => $this->overview[$locale] ?? $this->overview['en'] ?? null,
            'first_air_date' => $this->first_air_date,
        ];
    }
}
