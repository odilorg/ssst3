<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Only admin users can view the printable cost estimate.
     * This prevents unauthenticated or non-admin users from
     * accessing full supplier pricing data via a guessable URL.
     */
    public function viewEstimate(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }
}
