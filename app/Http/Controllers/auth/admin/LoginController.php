<?php

namespace App\Http\Controllers\auth\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\shop\LoginRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->only('mobile', 'password');

        $admin = Admin::where('mobile', $credentials['mobile'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $admin->createToken('Admin API Token')->accessToken;

        return response()->json([
            'token' => $token,
            'data' => $admin
        ], 200);
    }
}
