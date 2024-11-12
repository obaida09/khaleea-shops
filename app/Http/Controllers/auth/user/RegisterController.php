<?php

namespace App\Http\Controllers\auth\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\user\RegesterRequest;
use App\Models\User;
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
            $user = User::create($data);

            $token = $user->createToken('API Token')->accessToken;

            // Commit transaction if everything is fine
            DB::commit();
            return response()->json([
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            // Rollback transaction in case of an error
            DB::rollBack();

            // Log the error message
            \Log::error('User Registration Error: ' . $e->getMessage());

            return response()->json(['error' => 'Failed to register user. Please try again later.'], 500);
        }
    }
}
