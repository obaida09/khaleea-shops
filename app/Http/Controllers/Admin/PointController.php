<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function addPoints(Request $request, User $user)
    {
        $points = $request->input('points');

        $userPoints = $user->points()->firstOrCreate(['user_id' => $user->id]);
        $userPoints->increment('points', $points);

        return response()->json(['message' => 'Points added successfully!', 'points' => $userPoints->points]);
    }
}
