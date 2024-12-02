<?php

namespace App\Http\Controllers\auth\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTokenController extends Controller
{
    public function __invoke(Request $request)
    {
        $whoIsGuard = $request->input('guard', 'admin');

        // Ensure the user is authenticated
        if (Auth::guard($whoIsGuard)->check()) {
            $user = Auth::guard($whoIsGuard)->user(); // Retrieve the authenticated user

            // Assuming you're using the Spatie Role-Permission package
            // $role = $user->roles->pluck('name');

            return response()->json([
                'message' => 'Token is active',
                'user_id' => $user->id,
                'user_name' => $user->name,
                // 'admin_role' => $role
            ], 200); 
        }

        return response()->json(['message' => 'Token is invalid or expired'], 401);
    }
}
