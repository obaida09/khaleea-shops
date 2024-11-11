<?php

namespace App\Services;

use Twilio\Rest\Client;

class WhatsAppService
{
    protected $client;

    public function __construct()
    {
        // Initialize Twilio client with credentials from .env file
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    /**
     * Send a WhatsApp message.
     *
     * @param string $to      The recipient’s WhatsApp number
     * @param string $message The message content
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     */
    public function sendWhatsAppMessage($to, $message)
    {
        return $this->client->messages->create(
            "whatsapp:{$to}",
            [
                'from' => env('TWILIO_WHATSAPP_FROM'),
                'body' => $message,
            ]
        );
    }
}
