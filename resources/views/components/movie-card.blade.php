@props(['movie'])

<div class="card h-100">
    @if(!empty($movie['poster_path']))
        <img 
            src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}" 
            class="card-img-top" 
            alt="{{ $movie['title'] }}"
            style="height: 450px; object-fit: cover;"
        >
    @else
        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 450px;">
            <span class="text-white">{{ __('movies.no_poster') }}</span>
        </div>
    @endif
    
    <div class="card-body d-flex flex-column">
        <h6 class="card-title mb-2">{{ $movie['title'] }}</h6>
        
        <div class="mb-2">
            <span class="badge-rating px-2 py-1">⭐ {{ number_format($movie['vote_average'] ?? 0, 1) }}</span>
            <small class="text-muted ms-1">{{ substr($movie['release_date'] ?? '', 0, 4) }}</small>
        </div>
        
        <a href="{{ route('movies.import', $movie['id']) }}" class="btn btn-import btn-sm mt-auto">
            {{ __('movies.import_button') }}
        </a>
    </div>
</div>