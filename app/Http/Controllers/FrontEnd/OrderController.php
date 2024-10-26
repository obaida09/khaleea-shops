<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\FrontEnd\OrderResource;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    // Get all orders with their associated products
    public function userOrders()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('products')->paginate(10);
        return OrderResource::collection($orders);
    }

    // Get a specific order with its products
    public function showOrder($id)
    {
        $user = Auth::user();

        // Find the order that belongs to the authenticated user
        $order = Order::with('products')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return new OrderResource($order);
    }

    public function store(StoreOrderRequest $request)
    {
        $order = new Order();
        $order->user_id = Auth::id();
        $order->total_price = 0; // This will be calculated
        $order->status = 'pending'; // Example status
        $order->save();

        $totalPrice = 0;
        foreach ($request->products as $productData) {
            $product = Product::findOrFail($productData['id']);
            $quantity = $productData['quantity'];
            $order->products()->attach($product->id, ['quantity' => $quantity]);
            $totalPrice += $product->price * $quantity;
            $product->decrement('quantity', $quantity);
        }

        // Update total price
        $order->total_price = $totalPrice;

        // Apply coupon if provided
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            $order->applyCoupon($coupon);
        }

        $order->save();

        // Find all admins with the 'view-orders' permission
        $admins = User::permission('view-orders')->get();
        foreach ($admins as $admin) {
            $admin->notify(new OrderStatusNotification($order, 'created'));
        }

        // Notify the user about the new order
        $user = auth()->user();
        $user->notify(new OrderStatusNotification($order, 'created'));

        return new OrderResource($order->load('products', 'coupon'));
    }

    public function destroy(Order $order)
    {
        // Delete the order
        $order->delete();

        // Find all admins with the 'view-orders' permission
        $admins = User::permission('view-orders')->get();
        foreach ($admins as $admin) {
            $admin->notify(new OrderStatusNotification($order, 'deleted'));
        }

        // Notify the user about the deleted order
        $user = auth()->user();
        $user->notify(new OrderStatusNotification($order, 'deleted'));

        return response()->json(['message' => 'Order deleted'], 200);
    }
}
