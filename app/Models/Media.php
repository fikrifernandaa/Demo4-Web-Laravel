<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'title',
        'type',
        'genre',
        'score',
        'episodes',
        'aura',
        'synopsis',
    ];

    protected $casts = [
        'score'    => 'float',
        'episodes' => 'integer',
    ];

    /** Pengguna yang memasukkan media ini ke koleksinya. */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /** Query scope: filter berdasarkan judul (setara searchByTitle() di Modul 3). */
    public function scopeTitleLike(Builder $query, ?string $keyword): Builder
    {
        return $query->when(
            filled($keyword),
            fn (Builder $q) => $q->where('title', 'like', '%' . $keyword . '%')
        );
    }
}
