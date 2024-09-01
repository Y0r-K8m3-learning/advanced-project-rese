<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantConttoller;
use App\Http\Controllers\MyPageConttoller;


Route::get('/reservation/complete', [RestaurantConttoller::class, 'complete'])->name('reservation.complete');

Route::get('/', [RestaurantConttoller::class, 'index'])->name('index');
Route::post('/favorite', [RestaurantConttoller::class, 'favorite'])->name('favorite.store');

Route::get('/register/complete', function () {
    return view('register_complete');
})->name('register.complete');

Route::get('/mypage', [MyPageConttoller::class, 'index'])->name('mypage.index');

Route::post('/reservations/{id}/delete', [MyPageConttoller::class, 'destroy'])->name('reservations.destroy');

Route::get('/restaurants', [RestaurantConttoller::class, 'index'])->name('restaurants.index');

Route::get('/restaurant/{id}', [RestaurantConttoller::class, 'detail'])->name('restaurant.detail');

Route::post('/restaurants/{id}/favorite', [RestaurantConttoller::class, 'favorite']);

Route::post('/restaurants/{id}/unfavorite', [RestaurantConttoller::class, 'favorite'])->name('restaurants.favorite');
Route::post('/restaurants/{id}/unfavorite', [RestaurantConttoller::class, 'unfavorite'])->name('restaurants.unfavorite');

Route::post('/reservations', [RestaurantConttoller::class, 'store'])->name('reservation.store');

Route::post('/restaurants/{id}/rate', [RestaurantConttoller::class, 'rate'])->name('restaurant.rate');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/reservations/{id}/edit', [RestaurantConttoller::class, 'edit'])->name('reservation.edit');
Route::put('/reservations/{id}', [RestaurantConttoller::class, 'update'])->name('reservation.update');
Route::get('/reservations', [RestaurantConttoller::class, 'index'])->name('reservations.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
