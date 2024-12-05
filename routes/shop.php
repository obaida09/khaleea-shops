<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopSide;

Route::group(['middleware' => ['auth:shop']], function () {

    Route::get('/shop/notifications', [ShopSide\NotificationsController::class, 'getShopNotifications']);

    Route::get('/shop/categories', [ShopSide\GetCategoryController::class, 'index']);
    Route::get('/shop/profile', [ShopSide\ShopDetailsController::class, 'profile']);
    Route::put('/shop/profile', [ShopSide\ShopDetailsController::class, 'updateProfile']);

    Route::apiResource('/shop/products', ShopSide\ProductController::class);
    // Delete a specific image for a product
    Route::delete('/shop/products/{productId}/images/{imageId}', [ShopSide\ProductController::class, 'deleteImage']);
    // Delete all images for a product
    Route::delete('/shop/products/{productId}/images', [ShopSide\ProductController::class, 'deleteAllImages']);
    // Upload multiple images for a product
    Route::post('/shop/products/{productId}/images', [ShopSide\ProductController::class, 'uploadImages']);
    // get Orders for shop
    Route::get('/shop/orders', [ShopSide\OrderController::class, 'index']);
    // Update Order Satus
    Route::put('/shop/orders/{id}', [ShopSide\OrderController::class, 'update']);
});
