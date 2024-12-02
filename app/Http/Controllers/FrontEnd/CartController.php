<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\FrontEnd\StoreCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // View cart items for the logged-in user
    public function index()
    {
        $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart is empty'], 200);
        }

        return new CartResource($cart);
    }

    // Add product to cart
    public function store(StoreCartRequest $request)
    {
        $product = Product::find($request->product_id);
        $user = Auth::user();

        // Find or create the cart for the user
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Check if product is already in the cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // If the product exists, update the quantity
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity
            ]);
        } else {
            // Add new item to the cart
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['message' => 'Product added to cart'], 201);
    }

    // Update cart item quantity
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($itemId);

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json(['message' => 'Cart item updated'], 200);
    }

    // Remove item from cart
    public function destroy($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart'], 200);
    }
}
