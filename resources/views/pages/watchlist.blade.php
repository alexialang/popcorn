@extends('layouts.app')

@section('title', __('watchlist.page_title'))

@section('content')
    <h1 class="mb-4">{{ __('watchlist.my_watchlist') }}</h1>

    {{-- Messages flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistiques --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="card-title">{{ $stats['total'] }}</h3>
                    <p class="card-text text-muted">{{ __('watchlist.total_movies') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="card-title text-warning">{{ $stats['to_watch'] }}</h3>
                    <p class="card-text text-muted">{{ __('watchlist.to_watch_count') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="card-title text-success">{{ $stats['watched'] }}</h3>
                    <p class="card-text text-muted">{{ __('watchlist.watched_count') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres et recherche --}}
    <div class="row mb-4">
        {{-- Filtre par statut --}}
        <div class="col-md-6">
            <form action="{{ route('watchlist.index') }}" method="GET">
                <div class="input-group">
                    <label class="input-group-text">{{ __('watchlist.filter_by_status') }}</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ (!$status || $status === 'all') ? 'selected' : '' }}>
                            {{ __('watchlist.all') }}
                        </option>
                        <option value="to_watch" {{ $status === 'to_watch' ? 'selected' : '' }}>
                            {{ __('watchlist.status_to_watch') }}
                        </option>
                        <option value="watched" {{ $status === 'watched' ? 'selected' : '' }}>
                            {{ __('watchlist.status_watched') }}
                        </option>
                    </select>
                </div>
            </form>
        </div>

        {{-- Barre de recherche --}}
        <div class="col-md-6">
            <form action="{{ route('watchlist.index') }}" method="GET">
                <div class="input-group">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="{{ __('watchlist.search_placeholder') }}"
                        value="{{ $search ?? '' }}"
                    >
                    <button class="btn btn-primary" type="submit">
                        {{ __('watchlist.search') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Liste des films --}}
    @if($items->count() > 0)
        <div class="row row-cols-1 g-4">
            @foreach($items as $item)
                <div class="col">
                    <div class="card">
                        <div class="row g-0">
                            {{-- Affiche --}}
                            <div class="col-md-2">
                                @if($item->movie->poster_path)
                                    <img 
                                        src="https://image.tmdb.org/t/p/w200{{ $item->movie->poster_path }}" 
                                        class="img-fluid rounded-start"
                                        alt="{{ $item->movie->title }}"
                                        style="height: 100%; object-fit: cover;"
                                    >
                                @else
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center h-100">
                                        <span>{{ __('movies.no_poster') }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Informations --}}
                            <div class="col-md-7">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $item->movie->title }}</h5>
                                    
                                    <p class="card-text">
                                        <span class="badge {{ $item->status->badgeClass() }}">
                                            {{ $item->status->label() }}
                                        </span>
                                        @if($item->movie->release_date)
                                            <span class="badge bg-secondary ms-2">
                                                {{ $item->movie->release_date->format('Y') }}
                                            </span>
                                        @endif
                                        @if($item->movie->vote_average)
                                            <span class="badge-rating px-2 py-1 ms-2">
                                                ⭐ {{ number_format($item->movie->vote_average, 1) }}
                                            </span>
                                        @endif
                                    </p>

                                    <p class="card-text">
                                        <small class="text-muted">
                                            {{ __('watchlist.added_on') }} {{ $item->created_at->format('d/m/Y') }}
                                        </small>
                                    </p>

                                    @if($item->movie->overview)
                                        <p class="card-text">
                                            {{ Str::limit($item->movie->overview, 150) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="col-md-3">
                                <div class="card-body d-flex flex-column h-100 justify-content-center">
                                    {{-- Voir les détails --}}
                                    <a href="{{ route('movies.show', $item->movie->id) }}" class="btn btn-primary btn-sm mb-2">
                                        {{ __('watchlist.view_details') }}
                                    </a>

                                    {{-- Toggle statut --}}
                                    <form action="{{ route('watchlist.updateStatus', $item) }}" method="POST" class="mb-2">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                                            @if($item->status === App\Enums\WatchlistStatus::TO_WATCH)
                                                {{ __('watchlist.mark_as_watched') }}
                                            @else
                                                {{ __('watchlist.mark_as_to_watch') }}
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Supprimer --}}
                                    <form action="{{ route('watchlist.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('{{ __('watchlist.confirm_delete') }}')">
                                            {{ __('watchlist.remove_from_watchlist') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $items->appends(['status' => $status, 'search' => $search])->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">
            <h4>{{ __('watchlist.no_items') }}</h4>
            <p>
                <a href="{{ route('index') }}" class="btn btn-primary">
                    {{ __('movies.search_title') }}
                </a>
            </p>
        </div>
    @endif
@endsection