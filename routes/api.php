<?php

use App\Http\Controllers\auth\admin as authAdmin;
use App\Http\Controllers\auth\shop;
use App\Http\Controllers\auth\user;
use App\Http\Controllers\ShopSide\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return response()->json([
        'message' => 'Unauthenticated. Please log in to access this resource.',
    ], 401);
})->name('login');

Route::post('/admin/register', authAdmin\RegisterController::class)->name('admin.register');
Route::post('/admin/login', authAdmin\LoginController::class)->name('admin.login');
Route::post('/admin/logout', authAdmin\LogoutController::class)->name('admin.logout');

Route::post('/shop/register', shop\RegisterController::class)->name('shop.register');
Route::post('/shop/login', shop\LoginController::class)->name('shop.login');
// Route::post('/shop/logout', shop\LogoutController::class)->name('shop.logout');
Route::post('shop/password-reset', [shop\ShopPasswordResetController::class, 'sendResetLink']);
Route::post('/shop/password-reset/confirm', [shop\ShopPasswordResetController::class, 'resetPassword']);

Route::post('/user/register', user\RegisterController::class)->name('user.register');
Route::post('/user/login', user\LoginController::class)->name('user.login');
Route::post('/user/logout', user\LogoutController::class)->name('user.logout');
Route::post('user/password-reset', [user\UserPasswordResetController::class, 'sendResetLink']);

Route::get('/user/checkToken', user\CheckTokenController::class);

// Routes for Admin Panel
require __DIR__.'/admin.php';
// Routes for Shop Side
require __DIR__.'/shop.php';
// Routes for Front end
require __DIR__.'/frontEnd.php';

// ssh root@145.223.117.211
// cd /var/www/html/khaleea-shops
