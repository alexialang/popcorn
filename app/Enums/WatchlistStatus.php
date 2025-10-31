<?php

namespace App\Enums;

enum WatchlistStatus: string
{
    case TO_WATCH = 'to_watch';
    case WATCHED = 'watched';

    public function label(): string
    {
        return match($this) {
            self::TO_WATCH => __('watchlist.status_to_watch'),
            self::WATCHED => __('watchlist.status_watched'),
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::TO_WATCH => 'bg-warning text-dark',
            self::WATCHED => 'bg-success',
        };
    }
}