<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $customer;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, Customer $customer)
    {
        $this->booking = $booking;
        $this->customer = $customer;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Booking Request Received - {$this->booking->tour->title}")
                    ->view('emails.bookings.confirmation-placeholder');
    }
}
