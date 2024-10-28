<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\FrontEnd;
use App\Http\Controllers\ShopSide;
use App\Http\Controllers\auth\admin as authAdmin;
use App\Http\Controllers\auth\shop;
use App\Http\Controllers\auth\user;
use App\Http\Controllers\Admin\RoleController;
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

Route::post('/user/register', user\RegisterController::class)->name('user.register');
Route::post('/user/login', user\LoginController::class)->name('user.login');
Route::post('/user/logout', user\LogoutController::class)->name('user.logout');

Route::get('/user/checkToken', user\CheckTokenController::class);


Route::group(['middleware' => ['auth:admin']], function () {

    /*
        ------
        Routes for Admin Panel
        ------
    */
    Route::apiResource('admin/roles', Admin\RoleController::class);
    Route::apiResource('admin/users', Admin\UserController::class);
    Route::apiResource('admin/shops', Admin\ShopController::class);
    Route::apiResource('admin/categories', Admin\CategoryController::class);
    Route::apiResource('admin/tags', Admin\TagController::class);
    Route::apiResource('admin/products', Admin\ProductController::class);
    Route::apiResource('admin/coupons', Admin\CouponController::class);
    Route::apiResource('admin/orders', Admin\OrderController::class);
    Route::apiResource('admin/colors', Admin\ColorsController::class);
    Route::apiResource('admin/sizes', Admin\SizesController::class);
    Route::apiResource('admin/posts', Admin\PostController::class);

    Route::get('/permissions', [RoleController::class, 'getPermissions']);
    Route::post('/users/{user}/points/add', [Admin\PointController::class, 'addPoints']);



    /*
        ------
        Routes for Front end
        ------
    */

    Route::get('/user/profile', [FrontEnd\UserDetailsController::class, 'profile']);
    Route::put('/user/profile', [FrontEnd\UserDetailsController::class, 'updateProfile']);

    Route::get('/user/orders', [FrontEnd\OrderController::class, 'userOrders']);
    Route::get('/user/order/{id}', [FrontEnd\OrderController::class, 'showOrder']);
    Route::post('/user/orders', [FrontEnd\OrderController::class, 'store']);
    Route::delete('/user/orders/{id}', [FrontEnd\OrderController::class, 'destroy']);

    // Route::apiResource('user/posts', FrontEnd\PostController::class);
    Route::apiResource('carts', FrontEnd\CartController::class);

    // List comments and their replies for a post
    Route::get('/posts/comments/{id}', [FrontEnd\CommentController::class, 'index']);
    // Add a new comment or reply to a comment
    Route::post('/posts/comments', [FrontEnd\CommentController::class, 'store']);
    // Delete a comment or reply
    Route::delete('/comments/{comment}', [FrontEnd\CommentController::class, 'destroy']);
});


/*
    ------
    Routes for Shop Side
    ------
*/

Route::group(['middleware' => ['auth:shop']], function () {

    Route::get('/shop/orders', [ShopSide\OrderController::class, 'index']);
    // Route::get('/shop/order/{id}', [FrontEnd\OrderController::class, 'showOrder']);
    // Route::post('/shop/orders', [FrontEnd\OrderController::class, 'store']);
    // Route::delete('/shop/orders/{id}', [FrontEnd\OrderController::class, 'destroy']);
});

Route::get('/verticalPage', [FrontEnd\PagesController::class, 'vertical']);
Route::get('/horizontalPage', [FrontEnd\PagesController::class, 'horizontal']);

Route::get('/product/{slug}', [FrontEnd\ProductController::class, 'show']);
