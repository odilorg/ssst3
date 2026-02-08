<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    protected $botToken;
    protected $adminChatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->adminChatId = config('services.telegram.admin_chat_id');
    }

    /**
     * Send booking notification to admin
     */
    public function sendBookingNotification($booking)
    {
        $message = $this->formatBookingMessage($booking);
        return $this->sendMessage($message);
    }

    /**
     * Send inquiry notification to admin
     */
    public function sendInquiryNotification($inquiry, $tour)
    {
        $message = $this->formatInquiryMessage($inquiry, $tour);
        return $this->sendMessage($message);
    }

    /**
     * Format booking message
     */
    protected function formatBookingMessage($booking)
    {
        $tour = $booking->tour;
        $customer = $booking->customer;

        $message = "🎉 *NEW BOOKING REQUEST*\n\n";
        $message .= "📋 *Reference:* `{$booking->reference}`\n";
        $message .= "👤 *Customer:* {$customer->name}\n";
        $message .= "📧 *Email:* {$customer->email}\n";

        if ($customer->phone) {
            $message .= "📞 *Phone:* {$customer->phone}\n";
        }

        if ($customer->country) {
            $message .= "🌍 *Country:* {$customer->country}\n";
        }

        $message .= "\n🗺️ *Tour:* {$tour->title}\n";
        $message .= "📅 *Date:* {$booking->start_date->format('F j, Y')}\n";

        if ($booking->end_date && $booking->start_date->ne($booking->end_date)) {
            $message .= "📅 *End Date:* {$booking->end_date->format('F j, Y')}\n";
        }

        $message .= "👥 *Guests:* {$booking->pax_total}\n";
        $message .= "💰 *Total:* \${$booking->total_price} {$booking->currency}\n";

        if ($booking->special_requests) {
            $message .= "\n📝 *Special Requests:*\n_{$booking->special_requests}_\n";
        }

        $adminUrl = config('app.url') . '/admin/bookings/' . $booking->id;
        $message .= "\n[View in Admin Panel]({$adminUrl})";

        return $message;
    }

    /**
     * Format inquiry message
     */
    protected function formatInquiryMessage($inquiry, $tour)
    {
        $message = "❓ *NEW QUESTION*\n\n";
        $message .= "📋 *Reference:* `{$inquiry->reference}`\n";
        $message .= "👤 *Customer:* {$inquiry->customer_name}\n";
        $message .= "📧 *Email:* {$inquiry->customer_email}\n\n";

        $message .= "🗺️ *Tour:* {$tour->title}\n\n";

        $message .= "💬 *Question:*\n";
        $message .= "_{$inquiry->message}_\n";

        $adminUrl = config('app.url') . '/admin/tour-inquiries/' . $inquiry->id;
        $message .= "\n[View in Admin Panel]({$adminUrl})";

        return $message;
    }

    /**
     * Send contact form notification to admin
     */
    public function sendContactNotification($contact)
    {
        $message = $this->formatContactMessage($contact);
        return $this->sendMessage($message);
    }

    /**
     * Format contact form message for Telegram
     */
    protected function formatContactMessage($contact)
    {
        $message = "📧 *NEW CONTACT FORM SUBMISSION*\n\n";
        $message .= "📋 *Reference:* `{$contact->reference}`\n";
        $message .= "👤 *Name:* {$contact->name}\n";
        $message .= "📧 *Email:* {$contact->email}\n";

        if ($contact->phone) {
            $message .= "📞 *Phone:* {$contact->phone}\n";
        }

        $message .= "\n💬 *Message:*\n_{$contact->message}_\n";

        $adminUrl = config('app.url') . '/admin/contacts/' . $contact->id;
        $message .= "\n[View in Admin Panel]({$adminUrl})";

        return $message;
    }

    /**
     * Send pay-later notification to admin
     */
    public function sendPayLaterNotification($booking, $reason)
    {
        $message = $this->formatPayLaterMessage($booking, $reason);
        return $this->sendMessage($message);
    }

    /**
     * Format pay-later message
     */
    protected function formatPayLaterMessage($booking, $reason)
    {
        $tour = $booking->tour;
        $customer = $booking->customer;

        $reasonText = $reason === 'gateway_failed'
            ? 'Payment gateway failed'
            : 'Customer chose to pay later';

        $emoji = $reason === 'gateway_failed' ? '⚠️' : '💳';

        $message = "{$emoji} *PAY LATER: {$booking->reference}*\n\n";
        $message .= "📋 *Reason:* {$reasonText}\n";
        $message .= "🗺️ *Tour:* " . ($tour ? $tour->title : 'N/A') . "\n";
        $message .= "👤 *Customer:* " . ($customer ? $customer->name : 'N/A') . "\n";
        $message .= "📧 *Email:* " . ($customer ? $customer->email : 'N/A') . "\n";

        if ($customer && $customer->phone) {
            $message .= "📞 *Phone:* {$customer->phone}\n";
        }

        $message .= "💰 *Amount:* \${$booking->total_price}\n";
        $message .= "\n🔔 *Action needed:* Send payment link or arrange cash payment\n";

        $adminUrl = config('app.url') . '/admin/bookings/' . $booking->id;
        $message .= "\n[View in Admin Panel]({$adminUrl})";

        return $message;
    }

    /**
     * Send message to Telegram
     */
    protected function sendMessage($message)
    {
        if (!$this->botToken || !$this->adminChatId) {
            Log::warning('Telegram credentials not configured');
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->adminChatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                Log::info('Telegram notification sent successfully');
                return true;
            } else {
                Log::error('Failed to send Telegram notification', [
                    'response' => $response->json()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Telegram notification error: ' . $e->getMessage());
            return false;
        }
    }
}
