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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

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

    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        // Start a transaction
        DB::beginTransaction();

        try {

            $validated = $request->validated();
            // $validated['shop_id'] = Auth::guard('shop')->user()->id;
            $validated['colors'] = json_encode($request->colors);
            $validated['sizes'] = json_encode($request->sizes);
            unset($validated['images']);

            $validated['category_id'] = '6cae7c4e-42e3-487a-ba66-a7cf0ba35f4d';
            $validated['shop_id'] = 'fdf50e03-67ed-4d7a-b54d-5fa5fac8c85e';

            $product = Product::create($validated);

            // Handle multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->storeAs('products',  uniqid() . '_' . $image->getClientOriginalName(), 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                    ]);
                }
            }
return $request->file('images');
            // Commit the transaction
            DB::commit();

            return response()->json([
                'data' => new ProductResource($product),
                'message' => 'Product Created',
            ], 200);
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();

            // Log the error for debugging
            Log::error('Product creation failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'An error occurred while creating the product',
                'details' => $e->getMessage()
            ], 500);
        }
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
