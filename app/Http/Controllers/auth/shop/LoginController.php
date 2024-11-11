<?php

namespace App\Http\Controllers\auth\shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\shop\LoginRequest;
use App\Http\Resources\ShopSide\ShopResource;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->only('mobile', 'password');

        $shop = Shop::where('mobile', $credentials['mobile'])->first();

        if (!$shop || !Hash::check($credentials['password'], $shop->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $shop->createToken('Admin API Token')->accessToken;

        return response()->json([
            'token' => $token,
            'shop' => new ShopResource($shop)
        ], 200);
    }
}
