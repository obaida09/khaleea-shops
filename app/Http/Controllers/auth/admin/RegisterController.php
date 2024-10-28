<?php

namespace App\Http\Controllers\auth\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\RegesterRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __invoke(RegesterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->password);
        $admin = Admin::create($data);
        // Assign the "admin" role to the newly created admin
        $admin->assignRole('admin');

        $token = $admin->createToken('API Token')->accessToken;

        return response()->json([
            'token' => $token
        ], 201);
    }
}
