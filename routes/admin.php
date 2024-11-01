<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\RoleController;

Route::group(['middleware' => ['auth:admin']], function () {

    Route::apiResource('admin/roles', Admin\RoleController::class);
    Route::apiResource('admin/users', Admin\UserController::class);
    Route::apiResource('admin/shops', Admin\ShopController::class);
    Route::apiResource('admin/categories', Admin\CategoryController::class);
    Route::apiResource('admin/tags', Admin\TagController::class);
    // Route::apiResource('admin/products', Admin\ProductController::class);
    Route::apiResource('admin/coupons', Admin\CouponController::class);
    Route::apiResource('admin/orders', Admin\OrderController::class);
    Route::apiResource('admin/colors', Admin\ColorsController::class);
    Route::apiResource('admin/sizes', Admin\SizesController::class);
    Route::apiResource('admin/posts', Admin\PostController::class);

    Route::get('/permissions', [RoleController::class, 'getPermissions']);
    Route::post('/users/{user}/points/add', [Admin\PointController::class, 'addPoints']);
});
