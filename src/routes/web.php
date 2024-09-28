<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\RestaurantConttoller;
use App\Http\Controllers\AdminConttoller;
use App\Http\Controllers\MyPageConttoller;
use App\Http\Controllers\StripePaymentsController;

Route::get('/register', [AuthController::class, 'getRegister']);
Route::post('/register', [AuthController::class, 'postRegister']);

Route::get('/login', [AuthController::class, 'getLogin'])->name('login');;
Route::post('/login', [AuthController::class, 'postLogin']);

Route::post('/admin/sendMailToAll', [MailController::class, 'sendMailToAll'])->name('admin.sendMailToAll');




Route::post('/admin/sendMail', [MailController::class, 'sendMail'])->name('admin.sendMail');

// Route::middleware(['auth', 'admin'])->group(function () {
//     Route::get('/admin/mail', [MailController::class, 'create'])->name('admin.mail.create');
//     Route::post('/admin/mail', [MailController::class, 'send'])->name('admin.mail.send');
// });
Route::get('/dashboard', [RestaurantConttoller::class, 'index'])->name('dashboard');
Route::get('/', [
    RestaurantConttoller::class,
    'index'
])->name('index');
Route::middleware(['auth', 'verified'])->group(function () {});

Route::post('/payment/index', [StripePaymentsController::class, 'index'])->name('paymentindex');
Route::post('/payment', [StripePaymentsController::class, 'payment'])->name('payment.store');
Route::get('/complete', [StripePaymentsController::class, 'complete'])->name('complete');

Route::middleware('auth')->group(function () {
    Route::get('/admin/owners', [AdminConttoller::class, 'index'])->name('admin.owners.index');
    Route::post('/admin/owners', [AdminConttoller::class, 'store'])->name('admin.owners.store');
});

// 店舗一覧
Route::get('/owner', [RestaurantConttoller::class, 'owner'])->name('owner');

// 予約一覧
Route::get('/owner/restaurants/{id}/reservations', [RestaurantConttoller::class, 'reservations'])->name('owner.restaurants.reservations');


// 店舗登録フォーム表示
Route::get('/restaurants/create', [RestaurantConttoller::class, 'owner_create'])->name('restaurants.create');


// 店舗登録
Route::post('/owner/restaurants/store', [RestaurantConttoller::class, 'owner_store'])->name('owner.restaurants.store');

// 店舗編集
Route::put('/owner/restaurants/{id}', [RestaurantConttoller::class, 'update'])->name('owner.restaurants.update');

// 予約一覧
Route::get('/owner/restaurants/{id}/reservations', [RestaurantConttoller::class, 'reservations'])->name('reservations');


Route::get('/reservations/verify/{id}', [RestaurantConttoller::class, 'verify'])->name('reservation.verify');

Route::get('/qrtest/{id}', [RestaurantConttoller::class, 'generateQrCode']);
Route::get('/reservations/{id}/qrcode', [RestaurantConttoller::class, 'showQrCode'])->name('reservation.qrcode');

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


Route::get('/reservations/{id}/edit', [RestaurantConttoller::class, 'edit'])->name('reservation.edit');
Route::put('/reservations/{id}', [RestaurantConttoller::class, 'update'])->name('reservation.update');
Route::get('/reservations', [RestaurantConttoller::class, 'index'])->name('reservations.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
