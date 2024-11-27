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

    Route::prefix('user')->as('user.')->apiResource('user/posts', FrontEnd\PostController::class);
    Route::apiResource('carts', FrontEnd\CartController::class);

    // List comments and their replies for a post
    Route::get('/posts/comments/{id}', [FrontEnd\CommentController::class, 'index']);
    // Add a new comment or reply to a comment
    Route::post('/posts/comments', [FrontEnd\CommentController::class, 'store']);
    // Delete a comment or reply
    Route::delete('/comments/{comment}', [FrontEnd\CommentController::class, 'destroy']);


    Route::post('user/products/{productId}/save', [FrontEnd\ProductController::class, 'saveProduct']);
    Route::delete('user/products/{productId}/unsave', [FrontEnd\ProductController::class, 'unsaveProduct']);
    Route::get('user/saved-products', [FrontEnd\ProductController::class, 'getSavedProducts']);

    Route::post('user/posts/{postId}/save', [FrontEnd\PostController::class, 'savePost']);
    Route::delete('user/posts/{postId}/unsave', [FrontEnd\PostController::class, 'unsavePost']);
    Route::get('user/saved-posts', [FrontEnd\PostController::class, 'getSavedPosts']);

    Route::post('user/product/{productId}/rate', [FrontEnd\ProductController::class, 'rateProduct']);
    Route::get('user/product/{productId}/ratings', [FrontEnd\ProductController::class, 'showProductRatings']);

    Route::post('user/product/discounts/apply', [FrontEnd\ProductController::class, 'applyDiscount']);

});

Route::get('/verticalPage', [FrontEnd\PagesController::class, 'vertical']);
Route::get('/horizontalPage', [FrontEnd\PagesController::class, 'horizontal']);

Route::get('/product/{slug}', [FrontEnd\ProductController::class, 'show']);
