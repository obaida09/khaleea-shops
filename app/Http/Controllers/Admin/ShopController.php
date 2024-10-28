<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use App\Http\Resources\ShopResource;
use App\Models\shop;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ShopController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:edit-users', only: ['update']),
            new Middleware('can:delete-users', only: ['destroy']),
            new Middleware('can:create-users', only: ['store']),
            new Middleware('can:view-users', only: ['index', 'show']),
        ];
    }
    public function index(Request $request)
    {
        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $query = shop::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $shops = $query->orderBy($sortField, $sortOrder)->paginate(10);

        return ShopResource::collection($shops);
    }

    public function store(StoreShopRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);

        Shop::create($request->all());

        return response()->json([
            'message' => 'Shop Created',
        ], 201);
    }

    public function show(Shop $shop)
    {
        return new ShopResource($shop);
    }

    public function update(UpdateShopRequest $request, Shop $shop)
    {
        $data = $request->all();
        unset($data['role']);

        // Add Password to data
        trim($request->password) != '' ? $data['password'] = bcrypt($request->password) : '';
        $shop->update($data);

        // Assign role if provided
        if ($request->has('role')) {
            $shop->syncRoles($request->role);
        }

        return response()->json([
            'data' => new ShopResource($shop),
            'message' => 'Shop Updated',
        ], 201);
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();
        return response()->json(null, 204);
    }
}
