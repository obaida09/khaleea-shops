<?php

namespace App\Http\Controllers\auth\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest as AdminLoginRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(AdminLoginRequest $request)
    {
        $credentials = $request->only('mobile', 'password');

        $admin = Admin::where('mobile', $credentials['mobile'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $admin->createToken('Admin API Token')->accessToken;

        return response()->json(['token' => $token], 200);
    }
}
