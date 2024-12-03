<?php

namespace App\Http\Controllers\ShopSide;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopSide\UpdateProfileRequest;
use App\Http\Resources\ShopSide\ShopResource;
use Illuminate\Support\Facades\Auth;

class ShopDetailsController extends Controller
{
    public function profile()
    {
        $shop = new ShopResource(Auth::user());
        return $shop;
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->all();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('shop_image', 'public');
            $data['image'] = $imagePath;
        }

        // Add Password to data
        trim($request->password) != '' ? $data['password'] = bcrypt($request->password) : '';
        $user->update($data);

        return response()->json([
            'data' => new ShopResource($user),
            'message' => 'User Updated',
        ], 201);
    }
}
