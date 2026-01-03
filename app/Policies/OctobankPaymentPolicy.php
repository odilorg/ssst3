<?php

namespace App\Policies;

use App\Models\OctobankPayment;
use App\Models\User;

class OctobankPaymentPolicy
{
    /**
     * Determine whether the user can refund the payment.
     */
    public function refund(User $user, OctobankPayment $payment): bool
    {
        return $user->isAdmin();
    }
}
