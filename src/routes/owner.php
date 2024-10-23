<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::middleware('auth')->group(function () {
    Route::get('/admin/owners', [AdminController::class, 'index'])->name('admin.owners.index');
    Route::post('/admin/owners', [AdminController::class, 'store'])->name('admin.owners.store');
});
