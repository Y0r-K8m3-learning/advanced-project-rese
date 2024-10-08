<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminConttoller;

Route::middleware('auth')->group(function () {
    Route::get('/admin/owners', [AdminConttoller::class, 'index'])->name('admin.owners.index');
    Route::post('/admin/owners', [AdminConttoller::class, 'store'])->name('admin.owners.store');
});
