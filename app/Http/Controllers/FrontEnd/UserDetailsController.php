<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\FrontEnd\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class UserDetailsController extends Controller
{
    public function profile()
    {
       $user = new UserResource(Auth::user());
       return $user;
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->all();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('user_image', 'public');
            $data['image'] = $imagePath;
        }

        // Add Password to data
        trim($request->password) != '' ? $data['password'] = bcrypt($request->password):'';
        $user->update($data);

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'User Updated',
        ], 201);
    }
}
