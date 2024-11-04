<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopSide;

Route::group(['middleware' => ['auth:shop']], function ()
{

    Route::get('/shop/profile', [ShopSide\ShopDetailsController::class, 'profile']);
    Route::put('/shop/profile', [ShopSide\ShopDetailsController::class, 'updateProfile']);

    Route::apiResource('/shop/products', ShopSide\ProductController::class);

    Route::get('/shop/orders', [ShopSide\OrderController::class, 'index']);
    // Route::get('/shop/order/{id}', [ShopSide\OrderController::class, 'showOrder']);
    // Route::post('/shop/orders', [ShopSide\OrderController::class, 'store']);
    // Route::delete('/shop/orders/{id}', [ShopSide\OrderController::class, 'destroy']);
});
