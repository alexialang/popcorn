@extends('layouts.app')

@section('title', __('movies.search_results'))

@section('content')
    <h1 class="mb-4">{{ __('movies.search_results') }}</h1>

    <form action="{{ route('movies.search') }}" method="GET">
                <div class="input-group mb-3">
                    <input 
                        type="text" 
                        name="query" 
                        class="form-control form-control-lg" 
                        placeholder="{{ __('movies.search_placeholder') }}"
                        value="{{ $query ?? '' }}" 
                        required
                    >
                    <button class="btn btn-primary btn-lg" type="submit">
                        {{ __('movies.search_button') }}
                    </button>
                </div>
            </form>
    
    <a href="{{ route('index') }}" class="btn btn-secondary mb-4">← {{ __('movies.back_to_search') }}</a>
    
    {{-- Message d'erreur API --}}
    @if(isset($results['error']) && $results['error'])
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ __('movies.api_error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(isset($results['results']) && count($results['results']) > 0)
        {{-- Informations de pagination --}}
        <div class="mb-3">
            <p class="text-muted">
                {{ __('movies.pagination_info', [
                    'page' => $results['page'] ?? 1,
                    'total_pages' => $results['total_pages'] ?? 1,
                    'total_results' => $results['total_results'] ?? 0
                ]) }}
            </p>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach($results['results'] as $movie)
                <div class="col">
                    <x-movie-card :movie="$movie" />
                </div>
            @endforeach
        </div>

        {{-- Pagination simple --}}
        @if(isset($results['total_pages']) && $results['total_pages'] > 1)
            <nav class="mt-4" aria-label="Navigation des résultats">
                <ul class="pagination justify-content-center">
                    <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('movies.search', ['query' => $query, 'page' => $currentPage - 1]) }}">
                            {{ __('movies.previous') }}
                        </a>
                    </li>
                    
                    <li class="page-item active">
                        <span class="page-link">{{ $currentPage }} / {{ $results['total_pages'] }}</span>
                    </li>
                    
                    <li class="page-item {{ $currentPage >= $results['total_pages'] ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('movies.search', ['query' => $query, 'page' => $currentPage + 1]) }}">
                            {{ __('movies.next') }}
                        </a>
                    </li>
                </ul>
            </nav>
        @endif
    @else
        <div class="alert alert-info">
            {{ __('movies.no_results') }}
        </div>
    @endif
@endsection