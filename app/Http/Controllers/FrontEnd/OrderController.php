<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\FrontEnd\StoreOrderRequest;
use App\Http\Resources\FrontEnd\OrderResource;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Group products by store
        $groupedProducts = collect($request->products)->groupBy(function ($productData) {
            $product = Product::find($productData['id']);
            return $product->shop_id;
        });


        DB::beginTransaction();

        try {
            $orders = [];

            foreach ($groupedProducts as $shopId => $products) {

                $order = new Order();
                $order->shop_id = $shopId; // Link the order to the store
                $order->user_id = Auth::id();
                $order->total_price = 0; // This will be calculated
                $order->status = 'pending'; // Example status

                $order->save();

                $totalPrice = 0;

                foreach ($products  as $productData) {
                    $product = Product::findOrFail($productData['id']);
                    $quantity = $productData['quantity'];
                    $order->products()->attach($product->id, [
                        'quantity' => $quantity,
                        'price' => $product->price,
                        'product_color' => $productData['product_color'],
                        'product_size' => $productData['product_size'],
                    ]);
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
                $orders[] = $order; // Add to the response array

                // Find all admins with the 'view-orders' permission
                $admins = Admin::permission('view-orders')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new OrderStatusNotification($order, 'created'));
                }

                // Notify the user about the new order
                $user = auth()->user();
                $user->notify(new OrderStatusNotification($order, 'created'));
            }

            // Delete cart items for the user
            Cart::where('user_id', Auth::id())->delete();

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Orders created successfully!',
                'orders' => OrderResource::collection($orders)
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            return response()->json(['error' => 'Failed to create order: ' . $e->getMessage()], 500);
        }
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
