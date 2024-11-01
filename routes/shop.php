<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopSide;

Route::group(['middleware' => ['auth:shop']], function ()
{
    Route::apiResource('/shop/products', ShopSide\ProductController::class);

    Route::get('/shop/orders', [ShopSide\OrderController::class, 'index']);
    // Route::get('/shop/order/{id}', [FrontEnd\OrderController::class, 'showOrder']);
    // Route::post('/shop/orders', [FrontEnd\OrderController::class, 'store']);
    // Route::delete('/shop/orders/{id}', [FrontEnd\OrderController::class, 'destroy']);
});
