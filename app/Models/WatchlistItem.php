<?php

namespace App\Models;

use App\Enums\WatchlistStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchlistItem extends Model
{
     protected $fillable = [
        'movie_id',
        'status',
        'notes',
    ];

     protected $casts = [
        'status' => WatchlistStatus::class,
    ];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
}
