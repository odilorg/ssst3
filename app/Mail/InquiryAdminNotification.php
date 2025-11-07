<?php

namespace App\Mail;

use App\Models\Tour;
use App\Models\TourInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquiryAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $inquiry;
    public $tour;

    /**
     * Create a new message instance.
     */
    public function __construct(TourInquiry $inquiry, Tour $tour)
    {
        $this->inquiry = $inquiry;
        $this->tour = $tour;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("💬 New Tour Inquiry: {$this->inquiry->reference}")
                    ->markdown('emails.inquiries.admin-notification');
    }
}
