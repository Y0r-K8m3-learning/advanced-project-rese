<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Auth;

use App\Models\User;


Route::middleware('auth', 'verified', RoleMiddleware::class . ':' . User::ROLE_ADMIN)->group(function () {
    Route::get('/admin/owners', [AdminController::class, 'index'])->name('admin.owners.index');
    Route::post('/admin/owners', [AdminController::class, 'store'])->name('admin.owners.store');

    //csvアップロード
    Route::post('/admin/owners/csv', [AdminController::class, 'csvImportStore'])->name('admin.csv.store');

});
