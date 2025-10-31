@extends('layouts.app')

@section('title', __('movies.page_title'))

@section('content')
    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-md-8 col-lg-6">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold mb-3">{{ __('movies.home_title') }}</h1>
                <p class="lead text-muted">{{ __('movies.home_subtitle') }}</p>
            </div>
            
            <form action="{{ route('movies.search') }}" method="GET">
                <div class="input-group input-group-lg shadow-sm">
                    <input 
                        type="text" 
                        name="query" 
                        class="form-control py-3" 
                        placeholder="{{ __('movies.search_placeholder') }}" 
                        required
                        style="font-size: 1.1rem;"
                    >
                    <button class="btn btn-primary px-5" type="submit" style="font-size: 1.1rem;">
                        {{ __('movies.search_button') }}
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('watchlist.index') }}" class="text-muted text-decoration-none">
                    {{ __('movies.view_watchlist') }}
                </a>
            </div>
        </div>
    </div>
@endsection