<?php

namespace App\Services;
use App\Models\Movie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ApiTmdbService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
    }

    /**
     * Retourne un client HTTP configuré selon l'environnement
     * En local : SSL désactivé (problème certificats Windows)
     * En production : SSL activé pour la sécurité
     */
    private function getHttpClient()
    {
        return app()->environment('local') 
            ? Http::withoutVerifying() 
            : Http::withOptions(['verify' => true]);
    }

    public function searchMovies(string $query, int $page = 1)
    {
        $cacheKey = "tmdb_search_" . md5($query . '_page_' . $page);
        
        try {
            return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($query, $page) {
                $response = $this->getHttpClient()->timeout(10)->get('https://api.themoviedb.org/3/search/movie', [
                    'api_key' => config('services.tmdb.api_key'),
                    'language' => 'fr-FR',
                    'query' => $query,
                    'page' => $page,
                ]);
                
                if ($response->failed()) {
                    Log::error('TMDb Search API error', ['status' => $response->status()]);
                    return ['results' => [], 'error' => true];
                }
                
                return $response->json();
            });
        } catch (\Exception $e) {
            Log::error('TMDb Search API exception', ['message' => $e->getMessage()]);
            return ['results' => [], 'error' => true];
        }
    }
    public function getMovieDetails(int $tmdbId)
    {
        try {
            $response = $this->getHttpClient()->timeout(10)->get("https://api.themoviedb.org/3/movie/{$tmdbId}", [
                'api_key' => config('services.tmdb.api_key'),
                'language' => 'fr-FR',
            ]);
            
            if ($response->failed()) {
                Log::error('TMDb Movie Details API error', ['tmdb_id' => $tmdbId, 'status' => $response->status()]);
                throw new \Exception('Impossible de récupérer les détails du film');
            }
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('TMDb Movie Details exception', ['tmdb_id' => $tmdbId, 'message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getMovieCredits(int $tmdbId)
    {
        try {
            $response = $this->getHttpClient()->timeout(10)->get("https://api.themoviedb.org/3/movie/{$tmdbId}/credits", [
                'api_key' => config('services.tmdb.api_key'),
                'language' => 'fr-FR',
            ]);
            
            if ($response->failed()) {
                Log::error('TMDb Movie Credits API error', ['tmdb_id' => $tmdbId, 'status' => $response->status()]);
                throw new \Exception('Impossible de récupérer les crédits du film');
            }
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('TMDb Movie Credits exception', ['tmdb_id' => $tmdbId, 'message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function importMovie(int $tmdbId)
    {
        try {
            $movieData = $this->getMovieDetails($tmdbId);
            $creditsData = $this->getMovieCredits($tmdbId);
            
            return Movie::updateOrCreate(
                ['tmdb_id' => $tmdbId],
                [
                    'title' => $movieData['title'],
                    'original_title' => $movieData['original_title'] ?? null,
                    'overview' => $movieData['overview'] ?? null,
                    'poster_path' => $movieData['poster_path'] ?? null,
                    'backdrop_path' => $movieData['backdrop_path'] ?? null,
                    'release_date' => $movieData['release_date'] ?? null,
                    'vote_average' => $movieData['vote_average'] ?? 0,
                    'vote_count' => $movieData['vote_count'] ?? 0,
                    'popularity' => $movieData['popularity'] ?? 0,
                    'runtime' => $movieData['runtime'] ?? null,
                    'genres' => $this->extractGenres($movieData['genres'] ?? []),
                    'directors' => $this->extractDirectors($creditsData),
                    'cast' => $this->extractCast($creditsData),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Movie import failed', ['tmdb_id' => $tmdbId, 'message' => $e->getMessage()]);
            return null;
        }
    }

    private function extractGenres(array $genres): array
    {
        return array_map(fn($genre) => $genre['name'], $genres);
    }

    private function extractDirectors(array $credits): array
    {
        $directors = [];
        foreach ($credits['crew'] ?? [] as $member) {
            if (($member['job'] ?? '') === 'Director') {
                $directors[] = [
                    'name' => $member['name'],
                    'profile_path' => $member['profile_path'] ?? null,
                ];
            }
        }
        return $directors;
    }

    private function extractCast(array $credits): array
    {
        $cast = array_slice($credits['cast'] ?? [], 0, 10); // 10 premiers acteurs
        return array_map(function($actor) {
            return [
                'name' => $actor['name'],
                'character' => $actor['character'] ?? '',
                'profile_path' => $actor['profile_path'] ?? null,
            ];
        }, $cast);
    } 
    
    public function getSimilarMovies(int $tmdbId, int $limit = 3): ?array
    {
        $cacheKey = "tmdb_similar_{$tmdbId}";
        
        try {
            return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($tmdbId, $limit) {
                $response = $this->getHttpClient()->timeout(10)->get("https://api.themoviedb.org/3/movie/{$tmdbId}/similar", [
                    'api_key' => config('services.tmdb.api_key'),
                    'language' => 'fr-FR',
                    'page' => 1,
                ]);
                
                if ($response->failed()) {
                    Log::warning('TMDb Similar Movies API error', ['tmdb_id' => $tmdbId, 'status' => $response->status()]);
                    return null;
                }
                
                $data = $response->json();
                $results = $data['results'] ?? [];
                
                return array_slice($results, 0, $limit);
            });
        } catch (\Exception $e) {
            Log::warning('TMDb Similar Movies exception', ['tmdb_id' => $tmdbId, 'message' => $e->getMessage()]);
            return null;
        }
    }
}