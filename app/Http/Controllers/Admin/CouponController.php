<?php

namespace App\Http\Controllers\Admin;

use App\Events\Test;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Notifications\NewCouponNotification;

class CouponController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:edit-coupons', only: ['update']),
            new Middleware('can:delete-coupons', only: ['destroy']),
            new Middleware('can:create-coupons', only: ['store']),
            new Middleware('can:view-coupons', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $query = Coupon::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $coupons = $query->orderBy($sortField, $sortOrder)->paginate(10);

        return CouponResource::collection($coupons);
    }

    public function store(StoreCouponRequest $request)
    {

        $mes = $request->all()['code'];

        broadcast(new Test($mes));

        $coupon = Coupon::create($request->all());

        // // Assuming you have a way to get the user(s) to notify
        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new NewCouponNotification($coupon));
        }

        return response()->json([
            'message' => 'Coupon Created',
        ], 201);
    }

    public function show(Coupon $coupon)
    {
        return response()->json([
            'data' =>  new CouponResource($coupon),
            'message' => 'Coupon Getted',
        ], 201);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($request->all());

        return response()->json([
            'data' => new CouponResource($coupon),
            'message' => 'Coupon Updated',
        ], 201);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json(null, 204);
    }
}
