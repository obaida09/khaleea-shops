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
use App\Traits\ProductImagesUpload;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductController extends Controller
{
    use ProductImagesUpload;

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
        // Start a transaction
        DB::beginTransaction();

        try {

            $validated = $request->validated();
            $validated['shop_id'] = Auth::guard('shop')->user()->id;
            $validated['colors'] = json_encode($request->colors);
            $validated['sizes'] = json_encode($request->sizes);
            unset($validated['images']);

            $product = Product::create($validated);

            // Handle multiple images
            if ($request->hasFile('images')) {
                // Use the ProductImagesUpload trait
                $this->ProductImagesUpload($request->file('images'), $product->id, 'products/' . $product->slug . '/images');
            }

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
        $product->load('orders', 'images');
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


    /**
     * Delete a specific image for a product.
     *
     * @param $productId
     * @param $imageId
     * @return \Illuminate\Http\Response
     */
    public function deleteImage($productId, $imageId)
    {
        // Find the product and the image to delete
        $product = Product::findOrFail($productId);
        $image = ProductImage::findOrFail($imageId);

        // Delete the image file from storage
        Storage::disk('public')->delete($image->image_path);

        // Delete the image record from the database
        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully.',
        ], 200);
    }

    /**
     * Delete all images for a product.
     *
     * @param  $productId
     * @return \Illuminate\Http\Response
     */
    public function deleteAllImages($productId)
    {
        // Find the product
        $product = Product::findOrFail($productId);

        // Get all associated images
        $images = $product->images; // Assuming 'images' is the relationship method

        // Loop through images and delete from storage and database
        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        return response()->json([
            'message' => 'All images deleted successfully.',
        ], 200);
    }

    /**
     * Upload multiple images for a product.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $productId
     * @return \Illuminate\Http\Response
     */
    public function uploadImages(Request $request, int $productId)
    {
        // Validate the incoming request
        $request->validate([
            'images.*' => 'required|image|array|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the product by ID
        $product = Product::findOrFail($productId);

        // Use the ProductImagesUpload trait
        $this->ProductImagesUpload($request->file('images'), $product->id, 'products/' . $product->slug . '/images');

        return response()->json([
            'message' => 'Images uploaded successfully.',
            'product' => $product,
        ], 201);
    }
}
