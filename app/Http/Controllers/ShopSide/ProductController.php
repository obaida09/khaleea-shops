<?php

namespace App\Http\Controllers\ShopSide;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopSide\StoreProductRequest;
use App\Http\Requests\ShopSide\UpdateProductRequest;
use App\Http\Resources\ShopSide\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function index(Request $request)
    {

        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $shopId = Auth::guard('shop')->user()->id;
        $query = Product::query()->whereShopId($shopId)->with('shop');

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
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $validated['shop_id'] = Auth::guard('shop')->user()->id;
        unset($validated['images']);
        return $request->all();

        $product = Product::create($validated);

        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->storeAs('products',  uniqid() . '_' . $file->getClientOriginalName(),'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                ]);
            }
        }

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product Created',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('orders');
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

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
