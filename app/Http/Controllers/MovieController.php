<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\ApiTmdbService;

class MovieController extends Controller
{
    public function __construct(
        protected ApiTmdbService $apiTmdbService
    )
    {
    }
    
    public function index(): View
    {
        return view('pages.index');
    }

    public function searchMovies(Request $request): View
    {
        $query = $request->input('query');
        $page = $request->input('page', 1);
        $results = $this->apiTmdbService->searchMovies($query, $page);
        
        return view('pages.search', [
            'results' => $results,
            'query' => $query,
            'currentPage' => $page
        ]);
    }

    public function import(int $tmdbId): RedirectResponse
    {
        $movie = $this->apiTmdbService->importMovie($tmdbId);
        
        if ($movie) {
            return redirect()->route('movies.show', $movie->id)->with('success', __('movies.import_success'));
        }
        
        return back()->with('error', __('movies.import_error'));
    }

    public function show(int $id): View
    {
        $movie = Movie::findOrFail($id);
        $similarMovies = $this->apiTmdbService->getSimilarMovies($movie->tmdb_id, 3);
        
        return view('pages.movie-details', [
            'movie' => $movie,
            'similarMovies' => $similarMovies
        ]);
    }
}
