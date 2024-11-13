<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\FrontEnd\ProductResource;
use App\Models\Post;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;

class PagesController extends Controller
{
    public function vertical(Request $request)
    {
        // return ProductResource::collection(Product::with('images')->get());
        // Define total count you want to retrieve per request
        $totalCount = 25; // Adjust as needed

        // Calculate how many posts and products to retrieve
        $postsCount = (int) ($totalCount * 0.1); // 10%
        $productsCount = (int) ($totalCount * 0.6); // 60%
        $productsFromKhaleea = (int) ($totalCount * 0.3); // 30%

        // Get the current page from the request (defaults to 1)
        $currentPage = $request->get('page', 1);

        // Calculate the offset for pagination
        $offsetPosts = ($currentPage - 1) * $postsCount;
        $offsetProducts = ($currentPage - 1) * $productsCount;
        $offsetProductsFromKhaleea = ($currentPage - 1) * $productsFromKhaleea;

        // Fetch posts and products
        $posts = Post::with('user')->whereNull('product_id')->skip($offsetPosts)->take($postsCount)->get();

        $khaleeaShop = Shop::whereName('khaleea')->first();
        $productsFromKhaleea = Product::where('shop_id', $khaleeaShop->id)
            ->whereIn('season', [$khaleeaShop->season, 'all'])
            ->with('images')
            ->skip($offsetProductsFromKhaleea)
            ->take($productsFromKhaleea)
            ->get();


        $products = Product::join('shops', 'shops.id', '=', 'products.shop_id')
            ->select(
                'products.*', // Select all columns from the products table
                'shops.season as shop_season' // Alias the `season` column from the shops table
            )
            ->where(function ($query) {
                $query->whereColumn('shops.season', 'products.season')  // Match the season between shop and product
                    ->orWhere('products.season', 'all');
            })
            ->where('shop_id', '<>', $khaleeaShop->id)
            ->with('images')
            ->skip($offsetProducts)
            ->take($productsCount)
            ->get();

        $posts = PostResource::collection($posts);
        $products = ProductResource::collection($products);
        $productsFromKhaleea = ProductResource::collection($productsFromKhaleea);

        // Combine the results into a single collection
        $concatProducts = $productsFromKhaleea
            ->concat($products)
            ->concat($posts);

        // shuffle
        $mixedData = $concatProducts->shuffle();

        return response()->json($mixedData);
    }

    public function horizontal(Request $request)
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
