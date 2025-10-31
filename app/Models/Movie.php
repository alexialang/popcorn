<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Movie extends Model
{
    protected $fillable = [
        'tmdb_id',
        'title',
        'original_title',
        'overview',
        'poster_path',
        'backdrop_path',
        'release_date',
        'vote_average',
        'vote_count',
        'popularity',
        'genres',
        'directors',
        'cast',
        'runtime',
    ];

    protected $casts = [
        'genres' => 'array',
        'directors' => 'array',
        'cast' => 'array',
        'release_date' => 'date',
        'vote_average' => 'float',
        'vote_count' => 'integer',
        'popularity' => 'float',
        'runtime' => 'integer',
    ];
    
    public function watchlistItem(): HasOne
    {
        return $this->hasOne(WatchlistItem::class);
    }
}
