<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TMDb App')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body class="overflow-x-hidden">
    <header class="bg-dark text-white py-3 mb-4">
        <div class="container-fluid px-5">
            <nav class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('index') }}" class="text-white text-decoration-none">
                        <h3 class="mb-0">{{ config('app.name', 'Popcorn') }}</h3>
                    </a>
                    <a href="{{ route('index') }}" class="btn btn-outline-light">{{ __('movies.home') }}</a>
                </div>
                <a href="{{ route('watchlist.index') }}" class="btn btn-outline-light">{{ __('watchlist.my_watchlist') }}</a>
            </nav>
        </div>
    </header>

    <main class="container my-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>