<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProductResource;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function vertical(Request $request)
    {
        // Define total count you want to retrieve per request
        $totalCount = 27; // Adjust as needed

        // Calculate how many posts and products to retrieve
        $postsCount = (int) ($totalCount * 0.1); // 10%
        $productsCount = (int) ($totalCount * 0.9); // 90%

        // Get the current page from the request (defaults to 1)
        $currentPage = $request->get('page', 1);

        // Calculate the offset for pagination
        $offsetPosts = ($currentPage - 1) * $postsCount;
        $offsetProducts = ($currentPage - 1) * $productsCount;

        // Fetch posts and products
        $posts = Post::with('user')->whereNull('product_id')->skip($offsetPosts)->take($postsCount)->get();
        $products = Product::with('user', 'colors', 'sizes', 'images')->skip($offsetProducts)->take($productsCount)->get();

        $posts = PostResource::collection($posts);
        $products = ProductResource::collection($products);

        // Combine the results and shuffle
        $mixedData = $posts->concat($products)->shuffle();

        return response()->json($mixedData);
    }

    public function horizontal (Request $request)
    {
        // Define total count of posts you want to retrieve per request
        $totalCount = 10; // Adjust as needed

        // Calculate how many posts to retrieve with and without product ID
        $postsWithProductCount = (int) ($totalCount * 0.5); // 50%
        $postsWithoutProductCount = (int) ($totalCount * 0.5); // 50%

        // Get the current page from the request (defaults to 1)
        $currentPage = $request->get('page', 1);

        // Calculate the offset for pagination
        $offsetWithProduct = ($currentPage - 1) * $postsWithProductCount;
        $offsetWithoutProduct = ($currentPage - 1) * $postsWithoutProductCount;

        // Fetch posts with product ID
        $postsWithProduct = Post::with('product') // Assuming you have a relationship with Product
            ->whereNotNull('product_id') // Ensure we only get posts with a product ID
            ->skip($offsetWithProduct)
            ->take($postsWithProductCount)
            ->get();

        // Fetch posts without product ID
        $postsWithoutProduct = Post::with('product') // Assuming you have a relationship with Product
            ->whereNull('product_id') // Ensure we only get posts without a product ID
            ->skip($offsetWithoutProduct)
            ->take($postsWithoutProductCount)
            ->get();

        $postsWithProduct = PostResource::collection($postsWithProduct);
        $postsWithoutProduct = PostResource::collection($postsWithoutProduct);

        // Combine the results
        $mixedPosts = $postsWithProduct->concat($postsWithoutProduct);

        return response()->json($mixedPosts);
    }
}
