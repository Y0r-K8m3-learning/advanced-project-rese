<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware;
use App\Http\Middleware\RoleMiddleware;


Route::middleware('auth', 'verified', RoleMiddleware::class . ':' . User::ROLE_OWNER)->group(function () {
    Route::get('/owner', [RestaurantController::class, 'owner'])->name('owner');

    Route::get('/owner/restaurants/{id}/reservations', [RestaurantController::class, 'reservations'])->name('owner.restaurants.reservations');


    Route::get('/restaurants/create', [RestaurantController::class, 'owner_create'])->name('restaurants.create');


    Route::post('/owner/restaurants/store', [RestaurantController::class, 'owner_store'])->name('owner.restaurants.store');

    Route::put('/owner/restaurants/{id}', [RestaurantController::class, 'owner_update'])->name('owner.restaurants.update');

    Route::get('/owner/restaurants/{id}/reservations', [RestaurantController::class, 'reservations'])->name('reservations');
});
