<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StripePaymentsController;
use App\Models\User;
use App\Http\Middleware\RoleMiddleware;

//ユーザ登録
Route::get('/register', [AuthController::class, 'getRegister']);
Route::post('/register', [AuthController::class, 'postRegister']);

//ログイン
Route::get('/login', [AuthController::class, 'getLogin'])->name('login');;
Route::post('/login', [AuthController::class, 'postLogin']);
Route::get('/restaurant/{id}', [RestaurantController::class, 'detail'])->name('restaurant.detail');

Route::get('/complete', [StripePaymentsController::class, 'complete'])->name('complete');

Route::get('/reservation/complete', [RestaurantController::class, 'complete'])->name('reservation.complete');

Route::get('/', [RestaurantController::class, 'index'])->name('index');
Route::get('/', [RestaurantController::class, 'index'])->name('home');

Route::get('/register/complete', function () {
    return view('register_complete');
})->name('register.complete');

Route::get('/reservation/complete', function () {
    return view('reservation_complete');
});

Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');






Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/admin/sendMailToAll', [MailController::class, 'sendMailToAll'])->name('admin.sendMailToAll');


    Route::post('/admin/sendMail', [MailController::class, 'sendMail'])->name('admin.sendMail');

    Route::post('/payment/index', [StripePaymentsController::class, 'index'])->name('paymentindex');
    Route::post('/payment', [StripePaymentsController::class, 'payment'])->name('payment.store');




    Route::get('/reservations/verify/{id}', [RestaurantController::class, 'verify'])->name('reservation.verify');

    Route::get('/qrtest/{id}', [RestaurantController::class, 'generateQrCode']);
    Route::get('/reservations/{id}/qrcode', [RestaurantController::class, 'showQrCode'])->name('reservation.qrcode');


    Route::post('/favorite', [RestaurantController::class, 'favorite'])->name('favorite.store');

    Route::post('/reservations/{id}/delete', [MyPageController::class, 'destroy'])->name('reservations.destroy');

    Route::post('/restaurants/{id}/favorite', [RestaurantController::class, 'favorite']);

    Route::post('/restaurants/{id}/unfavorite', [RestaurantController::class, 'favorite'])->name('restaurants.favorite');
    Route::post('/restaurants/{id}/unfavorite', [RestaurantController::class, 'unfavorite'])->name('restaurants.unfavorite');




    Route::post('/reservations', [RestaurantController::class, 'store'])->name('reservation.store');

    Route::post('/restaurants/{id}/rate', [RestaurantController::class, 'rate'])->name('restaurant.rate');


    Route::get('/reservations/{id}/edit', [RestaurantController::class, 'edit'])->name('reservation.edit');
    Route::put('/reservations/{id}', [RestaurantController::class, 'update'])->name('reservation.update');
    Route::get('/reservations', [RestaurantController::class, 'index'])->name('reservations.index');

    //ログイン
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage.index');


    /**===pro課題 レビュー画面**/
    Route::middleware(RoleMiddleware::class . ':' . User::ROLE_USER)->group(function () {
        //一般ユーザのみ

        Route::post('/restaurants/review', [ReviewController::class, 'store'])->name('review.store');

        Route::get('/restaurants/review/{restaurant_id}', [ReviewController::class, 'create'])->name('review.create');
        Route::post('/restaurants/review', [ReviewController::class, 'store'])->name('review.store');

        Route::get('/restaurants/review/edit/{restaurant_id}/{review_id}', [ReviewController::class, 'edit'])->name('review.edit');
        Route::patch('/restaurants/review/update', [ReviewController::class, 'update'])->name('review.update');
    });


    Route::middleware([RoleMiddleware::class . ':' . User::ROLE_ADMIN . ',' . User::ROLE_USER])->group(function () {
        // 管理者とユーザ
        Route::get('/restaurants/review/delete/{restaurant_id}/{review_id}', [ReviewController::class, 'delete'])->name('review.delete');

        Route::delete('/restaurants/review/destroy', [ReviewController::class, 'destroy'])->name('review.destroy');
    });





    Route::get('/restaurants/review/complete/{restaurant_id}', [ReviewController::class, 'complete'])->name('review.complete');
});

Route::get('/restaurants/review/show/{restaurant_id}', [ReviewController::class, 'show'])->name('review.show');
/**pro課題 レビュー画面====**/


require __DIR__ . '/auth.php';
require __DIR__ . '/owner.php';
require __DIR__ . '/admin.php';
