<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\StripePaymentsController;

Route::middleware(['auth', 'verified'])->group(function () {});
//ユーザ登録
Route::get('/register', [AuthController::class, 'getRegister']);
Route::post('/register', [AuthController::class, 'postRegister']);

//ログイン
Route::get('/login', [AuthController::class, 'getLogin'])->name('login');;
Route::post('/login', [AuthController::class, 'postLogin']);

Route::post('/admin/sendMailToAll', [MailController::class, 'sendMailToAll'])->name('admin.sendMailToAll');


Route::post('/admin/sendMail', [MailController::class, 'sendMail'])->name('admin.sendMail');

Route::get('/', [
    RestaurantController::class,
    'index'
])->name('index');
Route::middleware(['auth', 'verified'])->group(function () {});

Route::post('/payment/index', [StripePaymentsController::class, 'index'])->name('paymentindex');
Route::post('/payment', [StripePaymentsController::class, 'payment'])->name('payment.store');
Route::get('/complete', [StripePaymentsController::class, 'complete'])->name('complete');


// 店舗一覧
Route::get('/owner', [RestaurantController::class, 'owner'])->name('owner');

// 店舗予約一覧
Route::get('/owner/restaurants/{id}/reservations', [RestaurantController::class, 'reservations'])->name('owner.restaurants.reservations');


// 店舗登録フォーム表示
Route::get('/restaurants/create', [RestaurantController::class, 'owner_create'])->name('restaurants.create');


// 店舗登録
Route::post('/owner/restaurants/store', [RestaurantController::class, 'owner_store'])->name('owner.restaurants.store');

// 店舗編集
Route::put('/owner/restaurants/{id}', [RestaurantController::class, 'update'])->name('owner.restaurants.update');

// 予約一覧
Route::get('/owner/restaurants/{id}/reservations', [RestaurantController::class, 'reservations'])->name('reservations');


Route::get('/reservations/verify/{id}', [RestaurantController::class, 'verify'])->name('reservation.verify');

Route::get('/qrtest/{id}', [RestaurantController::class, 'generateQrCode']);
Route::get('/reservations/{id}/qrcode', [RestaurantController::class, 'showQrCode'])->name('reservation.qrcode');

Route::get('/reservation/complete', [RestaurantController::class, 'complete'])->name('reservation.complete');

Route::get('/', [RestaurantController::class, 'index'])->name('index');
Route::get('/', [RestaurantController::class, 'index'])->name('home');
Route::post('/favorite', [RestaurantController::class, 'favorite'])->name('favorite.store');

Route::get('/register/complete', function () {
    return view('register_complete');
})->name('register.complete');

Route::get('/reservation/complete', function () {
    return view('reservation_complete');
});


Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage.index');

Route::post('/reservations/{id}/delete', [MyPageController::class, 'destroy'])->name('reservations.destroy');

Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');

Route::get('/restaurant/{id}', [RestaurantController::class, 'detail'])->name('restaurant.detail');

Route::post('/restaurants/{id}/favorite', [RestaurantController::class, 'favorite']);

Route::post('/restaurants/{id}/unfavorite', [RestaurantController::class, 'favorite'])->name('restaurants.favorite');
Route::post('/restaurants/{id}/unfavorite', [RestaurantController::class, 'unfavorite'])->name('restaurants.unfavorite');

Route::post('/reservations', [RestaurantController::class, 'store'])->name('reservation.store');

Route::post('/restaurants/{id}/rate', [RestaurantController::class, 'rate'])->name('restaurant.rate');


Route::get('/reservations/{id}/edit', [RestaurantController::class, 'edit'])->name('reservation.edit');
Route::put('/reservations/{id}', [RestaurantController::class, 'update'])->name('reservation.update');
Route::get('/reservations', [RestaurantController::class, 'index'])->name('reservations.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/owner.php';
require __DIR__ . '/admin.php';
