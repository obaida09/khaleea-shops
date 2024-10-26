<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Resources\FrontEnd\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Retrieve the product by slug with its associated posts
        $product = Product::where('slug', $slug)
            ->whereStatus(true)
            ->with('posts', 'colors', 'sizes', 'images')
            ->firstOrFail();

        // Get 50% of products from the same category
        $sameCategoryProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id) // Exclude the current product
            ->inRandomOrder()
            ->whereStatus(true)
            ->take(12)
            ->get();

        // Get 50% of products from different categories
        $differentCategoryProducts = Product::where('id', '!=', $product->id)
            ->where('category_id', '!=', $product->category_id) // Exclude products in the same category
            ->inRandomOrder()
            ->whereStatus(true)
            ->take(12)
            ->get();

        $sameCategoryProducts = ProductResource::collection($sameCategoryProducts);
        $differentCategoryProducts = ProductResource::collection($differentCategoryProducts);

        // Combine both sets of products
        $combinedProducts = $sameCategoryProducts->merge($differentCategoryProducts);
        // Shuffle the combined collection to randomize the final result
        $randomizedProducts = $combinedProducts->shuffle();

        return response()->json([
            'product' => new ProductResource($product),
            'related_products' => $randomizedProducts,
        ]);
    }
}
