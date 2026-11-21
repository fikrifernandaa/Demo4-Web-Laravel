<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'title'    => $this->title,
            'type'     => $this->type,
            'genre'    => $this->genre,
            'score'    => $this->score,
            'episodes' => $this->episodes,
            'aura'     => $this->aura,
            'synopsis' => $this->synopsis,

            // Relasi hanya muncul bila sudah di-load (with()/load()/withCount()).
            'users_count' => $this->whenCounted('users'),
            'users'       => $this->whenLoaded('users', fn () => $this->users->map(fn ($user) => [
                'id'       => $user->id,
                'name'     => $user->name,
                'added_at' => $user->pivot->created_at,
            ])),

            // Hanya muncul saat media diambil lewat relasi koleksi user (pivot).
            'added_at' => $this->whenPivotLoaded('media_user', fn () => $this->pivot->created_at),
        ];
    }
}
