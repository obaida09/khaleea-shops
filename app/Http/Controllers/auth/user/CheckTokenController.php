<?php

namespace App\Http\Controllers\auth\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CheckTokenController extends Controller
{
    public function __invoke(Request $request)
    {
        // Ensure the user is authenticated
        if (auth()->check()) {
            $user = auth()->user(); // Retrieve the authenticated user

            // Assuming you're using the Spatie Role-Permission package
            $role = $user->roles->pluck('name');

            return response()->json([
                'message' => 'Token is active',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $role
            ], 200);
        }

        return response()->json(['message' => 'Token is invalid or expired'], 401);
    }
}
