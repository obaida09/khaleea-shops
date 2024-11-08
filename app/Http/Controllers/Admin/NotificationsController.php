<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Notifications\Admin\StoreGroupMessage;
use App\Notifications\Admin\UserGroupMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NotificationsController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:send-message-to-stores', only: ['sendMessageToStores']),
            new Middleware('can:send-message-to-users', only: ['sendMessageToUsers']),
        ];
    }

    public function sendMessageToStores(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'message' => 'required|string|max:255',
            'shop_ids' => 'array|uuid|nullable', // Array of specific shop IDs if provided
            'shop_ids.*' => 'nullable|uuid|exists:shops,id',
        ]);

        $messageContent = $request->input('message');

        // Determine which shops to notify
        $shops = $request->input('shop_ids')
            ? Shop::whereIn('id', $request->input('shop_ids'))->get() // Specific shops
            : Shop::all(); // All shops

        // Send notification to each store
        foreach ($shops as $shop) {
            $shop->notify(new StoreGroupMessage($messageContent));
        }

        return response()->json(['message' => 'Notifications sent to all shops.']);
    }


    public function sendMessageToUsers(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'message' => 'required|string|max:255',
            'user_ids' => 'array|uuid|nullable', // Array of specific user IDs if provided
            'user_ids.*' => 'required|uuid|exists:users,id',
        ]);

        $messageContent = $request->input('message');

        User::chunk(100, function ($users) use ($messageContent) {
            Notification::send($users, new UserGroupMessage($messageContent));
        });

        // Send notification to each store
        // foreach ($users as $user) {
        //     $user->notify(new UserGroupMessage($messageContent));
        // }

        return response()->json(['message' => 'Notifications sent to all Users.']);
    }
}
