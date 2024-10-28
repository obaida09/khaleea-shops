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

        $query = Order::query();
return Auth::guard('shop')->user()->orders;
        $order = Auth::guard('shop')->user()->orders()->orderBy($sortField, $sortOrder)->paginate(10);
        return OrderResource::collection($order);
    }
}
