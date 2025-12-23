<?php

namespace App\Events;

use App\Models\OctobankPayment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSucceeded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public OctobankPayment $payment;

    public function __construct(OctobankPayment $payment)
    {
        $this->payment = $payment;
    }
}
