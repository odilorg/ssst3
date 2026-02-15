<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TripDetailsReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;
    public $tripDetailsUrl;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->tripDetailsUrl = $booking->getTripDetailsUrl();
    }

    public function build()
    {
        return $this->subject("Quick reminder: Share your trip details - {$this->booking->tour->title}")
                    ->markdown('emails.trip-details.reminder');
    }
}
