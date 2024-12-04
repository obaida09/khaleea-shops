<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function getUserNotifications()
    {
        $user = Auth::guard('user')->user();
        return $user->notifications;
    }
}
