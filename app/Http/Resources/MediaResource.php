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

            'users_count' => $this->whenCounted('users'),
            'users'       => $this->whenLoaded('users', fn () => $this->users->map(fn ($user) => [
                'id'       => $user->id,
                'name'     => $user->name,
                'added_at' => $user->pivot->created_at,
            ])),

            'added_at' => $this->whenPivotLoaded('media_user', fn () => $this->pivot->created_at),
        ];
    }
}
