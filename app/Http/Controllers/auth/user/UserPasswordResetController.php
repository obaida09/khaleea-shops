<?php

namespace App\Http\Controllers\auth\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Twilio\Rest\Client;

class UserPasswordResetController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate(['mobile' => 'required|exists:users,mobile']);

        // Generate reset token
        $token = Password::createToken(User::where('mobile', $request->mobile)->first());
return $token;
        // Generate the reset link
        $resetLink = url('/reset-password/' . $token);

        // Send reset link via WhatsApp
        $this->sendWhatsAppMessage($request->mobile, "Use this link to reset your password: $resetLink");

        return response()->json(['message' => 'Reset link sent to your WhatsApp']);
    }

    private function sendWhatsAppMessage($mobile, $message)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);

        $twilio->messages->create("whatsapp:$mobile", [
            'from' => env('TWILIO_WHATSAPP_FROM'),
            'body' => $message
        ]);
    }
}
