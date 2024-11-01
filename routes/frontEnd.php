<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontEnd;



Route::group(['middleware' => ['auth:user']], function () {

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

Route::get('/verticalPage', [FrontEnd\PagesController::class, 'vertical']);
Route::get('/horizontalPage', [FrontEnd\PagesController::class, 'horizontal']);

Route::get('/product/{slug}', [FrontEnd\ProductController::class, 'show']);
