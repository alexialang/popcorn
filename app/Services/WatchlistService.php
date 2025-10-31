<?php

namespace App\Services;

use App\Enums\WatchlistStatus;
use App\Models\Movie;
use App\Models\WatchlistItem;
use App\Repositories\WatchlistRepository;

class WatchlistService
{
    public function __construct(
        private WatchlistRepository $repository
    ) {}

    public function getAll(int $perPage = 12)
    {
        return $this->repository->findAll($perPage);
    }
    
    public function getByStatus(WatchlistStatus $status, int $perPage = 12)
    {
        return $this->repository->findByStatus($status, $perPage);
    }

    public function search(string $query, int $perPage = 12)
    {
        return $this->repository->search($query, $perPage);
    }

    public function add(Movie $movie, WatchlistStatus $status = WatchlistStatus::TO_WATCH, ?string $notes = null): WatchlistItem
    {
        $data = [
            'movie_id' => $movie->id,
            'status' => $status,
            'notes' => $notes,
        ];

        return $this->repository->create($data);
    }

    public function updateStatus(WatchlistItem $item, WatchlistStatus $status): bool
    {
        return $this->repository->update($item, ['status' => $status]);
    }

    public function remove(WatchlistItem $item): bool
    {
        return $this->repository->delete($item);
    }

    public function isInWatchlist(Movie $movie): ?WatchlistItem
    {
        return $movie->watchlistItem;
    }

    public function getStats(): array
    {
        return [
            'total' => $this->repository->countAll(),
            'to_watch' => $this->repository->countByStatus(WatchlistStatus::TO_WATCH),
            'watched' => $this->repository->countByStatus(WatchlistStatus::WATCHED),
        ];
    }
}