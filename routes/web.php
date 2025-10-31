<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\WatchlistController;

Route::get('/', [MovieController::class, 'index'])->name('index');

Route::get('/films', [MovieController::class, 'searchMovies'])->name('movies.search');
Route::get('/films/import/{tmdbId}', [MovieController::class, 'import'])->name('movies.import');
Route::get('/films/{id}', [MovieController::class, 'show'])->name('movies.show');

Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
Route::patch('/watchlist/{item}/status', [WatchlistController::class, 'updateStatus'])->name('watchlist.updateStatus');
Route::delete('/watchlist/{item}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');