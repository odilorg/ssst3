<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Mail\ContactFormSubmitted;
use App\Mail\ContactFormAutoReply;
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Store contact form submission
     */
    public function store(Request $request)
    {
        // Validation with friendly error messages
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:2000|min:10',
        ], [
            'name.required' => 'We need your name to address you properly',
            'name.min' => 'Please enter your full name',
            'email.required' => 'We need your email to reply to you',
            'email.email' => 'Please enter a valid email address',
            'message.required' => 'Please tell us how we can help you',
            'message.min' => 'Please provide more details (at least 10 characters)',
            'message.max' => 'Message is too long (maximum 2000 characters)',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check the form and try again',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Create contact record
            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'status' => 'new',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            // Send emails (don't fail if email fails)
            try {
                // Send notification to admin
                $adminEmail = config('mail.admin_email', 'admin@jahongir-hotels.uz');
                Mail::to($adminEmail)->send(new ContactFormSubmitted($contact));

                // Send auto-reply to customer
                Mail::to($contact->email)->send(new ContactFormAutoReply($contact));

            } catch (\Exception $e) {
                Log::error('Failed to send contact form emails: ' . $e->getMessage(), [
                    'contact_id' => $contact->id,
                    'email' => $contact->email,
                ]);
            }

            // Send Telegram notification
            try {
                $telegramService = new TelegramNotificationService();
                $telegramService->sendContactNotification($contact);
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram notification: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Thank you for contacting us! We will get back to you within 24 hours.',
                'contact' => [
                    'reference' => $contact->reference,
                    'name' => $contact->name,
                    'email' => $contact->email,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Contact form submission failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again or contact us via WhatsApp.'
            ], 500);
        }
    }
}
