@extends('layouts.app')

@section('title', $movie->title)

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <button onclick="history.back()" class="btn btn-secondary mb-4">← Retour</button>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                @if($movie->poster_path)
                    <img 
                        src="https://image.tmdb.org/t/p/w500{{ $movie->poster_path }}" 
                        alt="{{ $movie->title }}"
                        class="card-img-top rounded"
                        style="object-fit: cover;"
                    >
                @else
                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="height: 600px;">
                        <span>{{ __('movies.no_poster') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-8">
            <div class="mb-4">
                <h1 class="display-5 fw-bold mb-2">{{ $movie->title }}</h1>
                @if($movie->tagline)
                    <p class="text-muted fst-italic fs-5">{{ $movie->tagline }}</p>
                @endif
            </div>

            @if($movie->watchlistItem)
                <div class="card border-0 bg-light mb-4 p-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="badge {{ $movie->watchlistItem->status->badgeClass() }} px-3 py-2">
                            {{ $movie->watchlistItem->status->label() }}
                        </span>
                        
                        <form action="{{ route('watchlist.updateStatus', $movie->watchlistItem) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                @if($movie->watchlistItem->status === App\Enums\WatchlistStatus::TO_WATCH)
                                    {{ __('watchlist.mark_as_watched') }}
                                @else
                                    {{ __('watchlist.mark_as_to_watch') }}
                                @endif
                            </button>
                        </form>
                        
                        <form action="{{ route('watchlist.destroy', $movie->watchlistItem) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                {{ __('watchlist.remove_from_watchlist') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="mb-4">
                    <form action="{{ route('watchlist.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                        <input type="hidden" name="status" value="to_watch">
                        <button type="submit" class="btn btn-primary btn-lg">
                            + {{ __('watchlist.add_to_watchlist') }}
                        </button>
                    </form>
                </div>
            @endif
            <div class="mb-4 d-flex flex-wrap gap-2 align-items-center">
                <span class="badge-rating px-3 py-2 fs-6">
                    ⭐ {{ number_format($movie->vote_average, 1) }}/10
                </span>
                @if($movie->release_date)
                    <span class="badge-info px-3 py-2 fs-6">
                        {{ $movie->release_date->format('Y') }}
                    </span>
                @endif
                @if($movie->runtime)
                    <span class="badge-info px-3 py-2 fs-6">
                        {{ $movie->runtime }} min
                    </span>
                @endif
            </div>
            @if($movie->genres && count($movie->genres) > 0)
                <div class="mb-4">
                    <h6 class="mb-2">{{ __('movies.genres') }}</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($movie->genres as $genre)
                            <span class="badge badge-genre px-3 py-2">{{ $genre }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
            @if($movie->overview)
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">{{ __('movies.overview') }}</h5>
                    <p class="lead">{{ $movie->overview }}</p>
                </div>
            @endif
            @if($movie->directors && count($movie->directors) > 0)
                <div class="mb-4">
                    <h6 class="text-muted mb-2 text-uppercase small">{{ __('movies.directors') }}</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($movie->directors as $director)
                            <span class="badge bg-dark px-3 py-2" style="font-size: 0.95rem;">{{ $director['name'] }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
            @if($movie->cast && count($movie->cast) > 0)
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">{{ __('movies.cast') }}</h5>
                    <div class="row row-cols-3 row-cols-md-4 row-cols-lg-6 g-3">
                        @foreach(array_slice($movie->cast, 0, 6) as $actor)
                            <div class="col">
                                <div class="card h-100">
                                    @if(!empty($actor['profile_path']))
                                        <img 
                                            src="https://image.tmdb.org/t/p/w185{{ $actor['profile_path'] }}" 
                                            class="card-img-top"
                                            alt="{{ $actor['name'] }}"
                                            style="height: 220px; object-fit: cover;"
                                        >
                                    @else
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 220px;">
                                            <span style="font-size: 3rem;"></span>
                                        </div>
                                    @endif
                                    <div class="card-body p-2">
                                        <p class="card-text small mb-0"><strong>{{ $actor['name'] }}</strong></p>
                                        <p class="card-text small text-muted mb-0">{{ $actor['character'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    @if($similarMovies && count($similarMovies) > 0)
        <div class="mt-5 pt-4 border-top">
            <h3 class="mb-4">{{ __('movies.similar_movies') }}</h3>
            
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3 justify-content-center">
                @foreach($similarMovies as $similar)
                    <div class="col">
                        <x-movie-card :movie="$similar" />
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($similarMovies === null)
        <div class="alert alert-warning mt-5">
            {{ __('movies.similar_unavailable') }}
        </div>
    @endif
@endsection