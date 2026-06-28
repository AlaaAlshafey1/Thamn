<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Ensure the notification supports WhatsApp
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        // Get the phone number from the notifiable (e.g. User model)
        $phone = $notifiable->routeNotificationFor('whatsapp') ?? $notifiable->phone;
        
        if (!$phone) {
            return;
        }

        // Clean and format phone number for WhatsApp API
        $phone = $this->formatPhoneNumber($phone);
        
        // Get message text
        $message = $notification->toWhatsApp($notifiable);

        // Fetch credentials from env
        $instance = env('ULTRAMSG_INSTANCE');
        $token = env('ULTRAMSG_TOKEN');

        if (!$instance || !$token) {
            Log::error('WhatsAppChannel: Missing UltraMsg credentials.');
            return;
        }

        try {
            $url = "https://api.ultramsg.com/{$instance}/messages/chat";
            
            $response = Http::asForm()->post($url, [
                'token' => $token,
                'to' => $phone,
                'body' => $message,
            ]);

            if (!$response->successful()) {
                Log::error('WhatsAppChannel: Failed to send message.', [
                    'response' => $response->body(),
                    'phone' => $phone
                ]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsAppChannel: Exception while sending.', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
        }
    }

    /**
     * Format phone number to international format required by WhatsApp
     */
    private function formatPhoneNumber($phone)
    {
        // Remove spaces, dashes, parentheses
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle Saudi numbers starting with 05
        if (strpos($phone, '05') === 0 && strlen($phone) == 10) {
            return '966' . substr($phone, 1);
        }
        
        // Handle Saudi numbers starting with 5
        if (strpos($phone, '5') === 0 && strlen($phone) == 9) {
            return '966' . $phone;
        }
        
        // If it already starts with 966 or another country code, just return it
        return $phone;
    }
}
