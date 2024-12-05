<?php

namespace App\Http\Controllers\ShopSide;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopSide\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $shop = Auth::guard('shop')->user();

        // Retrieve all orders for products associated with the authenticated shop
        $orders = Order::whereHas('products', function ($query) use ($shop) {
            $query->where('shop_id', $shop->id);
        })->distinct()->orderBy($sortField, $sortOrder)->paginate(10);

        return OrderResource::collection($orders);
    }

    public function update(Request $request)
    {
        $shop = Auth::guard('shop')->user();

    }
}
