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
     * Send tour operator reminder (7/3/1 days before tour) to operator chat.
     * Used by SendTourOperatorReminders command instead of its own inline implementation.
     */
    public function sendTourOperatorReminder($booking, string $type): bool
    {
        $chatId = config('services.telegram.tour_operator_chat_id');
        if (!$chatId) {
            Log::warning('Telegram tour_operator_chat_id not configured');
            return false;
        }

        $daysUntil = $booking->daysUntilTour();
        $emoji = match($type) {
            '7_days' => '📅',
            '3_days' => '⚠️',
            '1_day'  => '🚨',
            default  => '📋',
        };
        $urgency = match($type) {
            '7_days' => 'TOUR IN 7 DAYS',
            '3_days' => 'TOUR IN 3 DAYS',
            '1_day'  => 'TOUR TOMORROW',
            default  => 'UPCOMING TOUR',
        };

        $customer = $booking->customer;
        $tour = $booking->tour;
        $passengerStatus = $booking->passenger_details_submitted_at ? '✅' : '⚠️ MISSING';
        $paymentStatus = $booking->payment_status === 'paid' ? '✅ Paid' : "⚠️ {$booking->payment_status}";

        // HTML parse mode — operator messages use rich formatting with status indicators
        $message  = "{$emoji} <b>{$urgency}</b>\n\n";
        $message .= "<b>Booking:</b> #{$booking->reference}\n";
        $message .= "<b>Tour:</b> " . ($tour?->title ?? 'N/A') . "\n";
        $message .= "<b>Date:</b> {$booking->start_date->format('M j, Y')} (in {$daysUntil} days)\n";
        $message .= "<b>Guests:</b> {$booking->pax_total}\n\n";
        $message .= "👤 <b>Customer:</b>\n";
        $message .= ($customer?->name ?? 'N/A') . "\n";
        $message .= "📧 " . ($customer?->email ?? 'N/A') . "\n";
        $message .= "📱 " . ($customer?->phone ?? 'N/A') . "\n\n";
        $message .= "📋 <b>Status:</b>\n";
        $message .= "• Passenger details: {$passengerStatus}\n";
        $message .= "• Payment: {$paymentStatus}\n";
        $message .= "• Driver: " . ($booking->driver_name ?: '⚠️ NOT ASSIGNED') . "\n";
        $message .= "• Guide: " . ($booking->guide_name ?: '⚠️ NOT ASSIGNED') . "\n";

        if ($booking->special_requests) {
            $message .= "\n📝 <b>Special requests:</b>\n{$booking->special_requests}\n";
        }

        $adminUrl = config('app.url') . '/admin/bookings/' . $booking->id;
        $message .= "\n<a href=\"{$adminUrl}\">View in Admin</a>";

        return $this->sendToChat($chatId, $message, 'HTML');
    }

    /**
     * Send payment success alert to admin chat.
     */
    public function sendPaymentSucceeded($payment): bool
    {
        $booking = $payment->booking;
        if (!$booking) {
            return false;
        }

        $customer = $booking->customer;
        $amountUsd = number_format($booking->total_price, 2);
        $amountUzs = number_format($payment->amount / 100, 0); // Octobank stores in tiyin

        $paymentTypeLabel = match($payment->payment_type ?? '') {
            'deposit' => 'Deposit (30%)',
            'full'    => 'Full payment',
            default   => ucfirst($payment->payment_type ?? 'Payment'),
        };

        $message  = "💳 *PAYMENT RECEIVED*\n\n";
        $message .= "📋 *Booking:* `{$booking->reference}`\n";
        $message .= "👤 *Customer:* " . ($customer?->name ?? 'N/A') . "\n";
        $message .= "📧 " . ($customer?->email ?? 'N/A') . "\n";
        $message .= "🗺️ *Tour:* " . ($booking->tour?->title ?? 'N/A') . "\n";
        $message .= "💰 *Type:* {$paymentTypeLabel}\n";
        $message .= "💵 *Amount:* \${$amountUsd} (~{$amountUzs} UZS)\n";

        $adminUrl = config('app.url') . '/admin/bookings/' . $booking->id;
        $message .= "\n[View Booking]({$adminUrl})";

        return $this->sendMessage($message);
    }

    /**
     * Send message to admin chat (Markdown parse mode).
     */
    protected function sendMessage($message): bool
    {
        return $this->sendToChat($this->adminChatId, $message, 'Markdown');
    }

    /**
     * Core HTTP send — handles credentials check, logging, and failure isolation.
     */
    protected function sendToChat(?string $chatId, string $message, string $parseMode = 'Markdown'): bool
    {
        if (!$this->botToken || !$chatId) {
            Log::warning('Telegram credentials not configured', ['chat_id_set' => (bool) $chatId]);
            return false;
        }

        // Telegram rejects messages over 4096 characters
        if (mb_strlen($message) > 4096) {
            $message = mb_substr($message, 0, 4050) . "\n\n[message truncated]";
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                Log::info('Telegram notification sent successfully');
                return true;
            }

            Log::error('Failed to send Telegram notification', [
                'response' => $response->json(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Telegram notification error: ' . $e->getMessage());
            return false;
        }
    }
}
