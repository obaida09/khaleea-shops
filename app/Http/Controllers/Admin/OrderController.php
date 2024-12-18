<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:edit-orders', only: ['update']),
            new Middleware('can:delete-orders', only: ['destroy']),
            new Middleware('can:view-orders', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $query = Order::query();

        $order = $query->orderBy($sortField, $sortOrder)->paginate(10);
        return OrderResource::collection($order);
    }


    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $order = Auth::user()->orders()->findOrFail($id);
        $newStatus = $request->input('status');

        if ($order->status !== $newStatus)
        {
            $order->status = $newStatus;
            $order->save();

            // Send notification to the user about the status change
            $order->user->notify(new OrderStatusNotification($order, $newStatus));
        }



        return new OrderResource($order);
    }

    public function destroy(string $id)
    {
        //
    }
}
