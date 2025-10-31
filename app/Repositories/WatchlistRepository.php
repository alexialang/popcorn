<?php

namespace App\Repositories;

use App\Models\WatchlistItem;
use App\Enums\WatchlistStatus;
use Illuminate\Pagination\LengthAwarePaginator;

class WatchlistRepository
{
    public function findAll(int $perPage = 12): LengthAwarePaginator
    {
        return WatchlistItem::with('movie')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByStatus(WatchlistStatus $status, int $perPage = 12): LengthAwarePaginator
    {
        return WatchlistItem::with('movie')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function search(string $query, int $perPage = 12): LengthAwarePaginator
    {
        return WatchlistItem::with('movie')
            ->whereHas('movie', function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): WatchlistItem
    {
        return WatchlistItem::create($data);
    }

    public function update(WatchlistItem $item, array $data): bool
    {
        return $item->update($data);
    }

    public function delete(WatchlistItem $item): bool
    {
        return $item->delete();
    }

    public function countAll(): int
    {
        return WatchlistItem::count();
    }

    public function countByStatus(WatchlistStatus $status): int
    {
        return WatchlistItem::where('status', $status)->count();
    }
}