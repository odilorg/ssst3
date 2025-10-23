<?php

namespace App\Observers;

use App\Models\TransportInstancePrice;

class TransportInstancePriceObserver
{
    /**
     * Handle the TransportInstancePrice "saving" event.
     *
     * Auto-delete records with empty/zero cost to maintain clean database.
     * This allows users to set 0, leave empty, or delete - all work the same.
     */
    public function saving(TransportInstancePrice $transportInstancePrice): bool
    {
        // If cost is empty, zero, or negative, delete the record instead of saving
        if (
            empty($transportInstancePrice->cost) ||
            (float) $transportInstancePrice->cost <= 0 ||
            empty($transportInstancePrice->price_type)
        ) {
            // If this is an existing record, delete it
            if ($transportInstancePrice->exists) {
                $transportInstancePrice->delete();
            }

            // Return false to prevent the save operation
            return false;
        }

        // Valid record - allow save
        return true;
    }
}
