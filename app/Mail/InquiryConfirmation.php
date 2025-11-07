<?php

namespace App\Mail;

use App\Models\Tour;
use App\Models\TourInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquiryConfirmation extends Mailable
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
        return $this->subject("Inquiry Received - {$this->tour->title}")
                    ->markdown('emails.inquiries.confirmation');
    }
}
