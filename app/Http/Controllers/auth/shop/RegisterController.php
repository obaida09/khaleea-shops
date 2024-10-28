<?php

namespace App\Http\Controllers\auth\shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\shop\RegesterRequest;
use App\Models\Shop;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __invoke(RegesterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->password);
        $shop = Shop::create($data);

        $token = $shop->createToken('API Token')->accessToken;

        return response()->json([
            'token' => $token
        ], 201);
    }
}
