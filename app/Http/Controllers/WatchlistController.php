<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\WatchlistItem;
use App\Services\WatchlistService;
use App\Enums\WatchlistStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WatchlistController extends Controller
{
    public function __construct(
        private WatchlistService $watchlistService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->input('status');
        $search = $request->input('search');

        if ($search) {
            $items = $this->watchlistService->search($search);
        }
        elseif ($status && $status !== 'all') {
            $statusEnum = WatchlistStatus::from($status);
            $items = $this->watchlistService->getByStatus($statusEnum);
        }
        else {
            $items = $this->watchlistService->getAll();
        }

        $stats = $this->watchlistService->getStats();

        return view('pages.watchlist', compact('items', 'stats', 'status', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(rules: [
            'movie_id' => 'required|exists:movies,id',
            'status' => 'required|in:to_watch,watched',
        ]);

        $movie = Movie::findOrFail($request->movie_id);
        $status = WatchlistStatus::from($request->status);

        try {
            $this->watchlistService->add($movie, $status);
            return back()->with('success', __('watchlist.added_success'));
        } catch (\Exception $e) {
            return back()->with('error', __('watchlist.added_error'));
        }
    }

    public function updateStatus(WatchlistItem $item): RedirectResponse
    {
        $newStatus = $item->status === WatchlistStatus::TO_WATCH 
            ? WatchlistStatus::WATCHED 
            : WatchlistStatus::TO_WATCH;
        
        $this->watchlistService->updateStatus($item, $newStatus);
        
        return back()->with('success', __('watchlist.status_updated'));
    }

    public function destroy(WatchlistItem $item): RedirectResponse
    {
        $this->watchlistService->remove($item);
        
        return back()->with('success', __('watchlist.removed_success'));
    }
}