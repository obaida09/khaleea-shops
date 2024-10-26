<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('has_permission')) {
    function has_permission($permission)
    {
        $user = Auth::user();
        if ($user && $user->can($permission)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('apiResponse')) {
    function apiResponse($data = null, $message = null, $status = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
