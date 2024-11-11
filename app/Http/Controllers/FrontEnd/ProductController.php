<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Resources\FrontEnd\ProductResource;
use App\Models\Product;
use App\Models\ProductDiscount;
use App\Models\ProductRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Retrieve the product by slug with its associated posts
        $product = Product::where('slug', $slug)
            ->whereStatus(true)
            ->with('posts', 'images')
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

    public function saveProduct(Request $request, $productId)
    {
        $user =  Auth::guard('user')->user();
        $product = Product::findOrFail($productId);

        // Attach the product to the user’s saved products if not already saved
        if (!$user->savedProducts()->where('product_id', $product->id)->exists()) {
            $user->savedProducts()->attach($product);
        }

        return response()->json(['message' => 'Product saved successfully.']);
    }

    public function unsaveProduct(Request $request, $productId)
    {
        $user =  Auth::guard('user')->user();
        $product = Product::findOrFail($productId);

        // Detach the product from the user’s saved products if saved
        $user->savedProducts()->detach($product);

        return response()->json(['message' => 'Product unsaved successfully.']);
    }

    public function rateProduct(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $product = Product::findOrFail($productId);

        $rating = new ProductRating([
            'user_id' => Auth::guard('user')->user()->id,
            'rating' => $request->input('rating'),
            'review' => $request->input('review'),
        ]);

        $product->ratings()->save($rating);

        return response()->json(['message' => 'Rating submitted successfully.'], 201);
    }

    public function showProductRatings($productId)
    {
        $product = Product::with('ratings.user')->findOrFail($productId);
        $averageRating = $product->averageRating();

        return response()->json([
            'product' => $product,
            'average_rating' => $averageRating,
            'ratings' => $product->ratings,
        ]);
    }

    public function applyDiscount(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $discount = ProductDiscount::updateOrCreate(
            [
                'shop_id' => Auth::guard('shop')->user()->id,
                'product_id' => $request->product_id,
            ],
            $request->only(['percentage', 'fixed_amount', 'start_date', 'end_date'])
        );

        return response()->json(['message' => 'Discount applied successfully.', 'discount' => $discount], 200);
    }
}
