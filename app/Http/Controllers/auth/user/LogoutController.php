<?php

namespace App\Http\Controllers\auth\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'token' => auth()->user()
        ], 201);
    }
}
