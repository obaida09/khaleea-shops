<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopSide\StoreProductRequest;
use App\Http\Requests\ShopSide\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:edit-products', only: ['update']),
            new Middleware('can:delete-products', only: ['destroy']),
            new Middleware('can:create-products', only: ['store']),
            new Middleware('can:manage-products', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {

        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $query = Product::query()->with('shop');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $products = $query->orderBy($sortField, $sortOrder)->whereStatus(1)->paginate(10);
        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['shop', 'images', 'posts']);
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        // Sync Colors and Sizes (replace old associations with new ones)
        $product->colors()->sync($request['colors']);
        $product->sizes()->sync($request['sizes']);

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product Updated',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product Deleted'], 204);
    }
}
