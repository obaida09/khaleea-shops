<?php

namespace App\Http\Controllers\auth\shop;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Shop;
use Illuminate\Support\Facades\Password;

class ShopPasswordResetController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Send a reset password link to the shop via WhatsApp.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['mobile' => 'required|exists:shops,mobile']);
        $shop = Shop::where('mobile', $request->mobile)->first();

        // Generate token
        $token = Password::createToken($shop);

        // Reset link or message content
        $resetLink = url('/reset-password/' . $token);
        $message = "Use this link to reset your password: $resetLink";

        // Send WhatsApp message
        $this->whatsAppService->sendWhatsAppMessage($request->mobile, $message);

        return response()->json(['message' => 'Password reset link sent via WhatsApp.']);
    }

    /**
     * Reset the password for the shop.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'mobile' => 'required|exists:shops,mobile',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $shop = Shop::where('mobile', $request->mobile)->first();
        $tokenValid = Password::tokenExists($shop, $request->token);

        if (!$tokenValid) {
            return response()->json(['error' => 'Invalid token.'], 400);
        }

        // Update password
        $shop->password = Hash::make($request->password);
        $shop->save();

        // Delete the token after resetting the password
        Password::deleteToken($shop);

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
