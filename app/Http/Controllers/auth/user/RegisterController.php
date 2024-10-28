<?php

namespace App\Http\Controllers\auth\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\user\RegesterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __invoke(RegesterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->password);
        $user = User::create($data);

        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'token' => $token
        ], 201);
    }
}
