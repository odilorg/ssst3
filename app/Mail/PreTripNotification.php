<?php

namespace App\Mail;

use App\Models\Booking;
use App\Services\ItineraryPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreTripNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        $mail = $this->subject("Tomorrow is the day! {$this->booking->tour->title}")
                    ->markdown('emails.pre-trip.notification');

        try {
            $pdfService = app(ItineraryPdfService::class);
            $mail->attachData(
                $pdfService->generateContent($this->booking),
                $pdfService->filename($this->booking),
                ['mime' => 'application/pdf']
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Could not attach itinerary PDF', [
                'booking_id' => $this->booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $mail;
    }
}
