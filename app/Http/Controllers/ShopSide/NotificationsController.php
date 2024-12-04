<?php

namespace App\Http\Controllers\ShopSide;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function getShopNotifications()
    {
        $shop = Auth::guard('shop')->user();
        return $shop->notifications;
    }
}
