<?php

namespace App\Http\Controllers\auth\shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopSide\RegesterRequest;
use App\Models\Shop;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function __invoke(RegesterRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $data['password'] = Hash::make($request->password);
            $shop = Shop::create($data);

            $token = $shop->createToken('API Token')->accessToken;

            // Commit transaction if everything is fine
            DB::commit();

            return response()->json(['token' => $token], 201);
        } catch (\Exception $e) {
            // Rollback transaction in case of an error
            DB::rollBack();

            // Log the error message
            \Log::error('User Registration Error: ' . $e->getMessage());

            return response()->json(['error' => 'Failed to register shop. Please try again later.'], 500);
        }
    }
}
