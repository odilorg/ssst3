<?php

namespace App\Observers;

use App\Models\Transport;
use App\Models\TransportPrice;
use App\Models\TransportInstancePrice;

class TransportObserver
{
    /**
     * Handle the Transport "created" event.
     * Auto-copy type prices to instance prices when a new transport is created.
     */
    public function created(Transport $transport): void
    {
        // Only auto-copy if transport has a type
        if (!$transport->transport_type_id) {
            return;
        }

        // Get all prices for this transport type
        $typePrices = TransportPrice::where('transport_type_id', $transport->transport_type_id)->get();

        if ($typePrices->isEmpty()) {
            return;
        }

        // Create instance prices from type prices
        foreach ($typePrices as $typePrice) {
            TransportInstancePrice::create([
                'transport_id' => $transport->id,
                'price_type' => $typePrice->price_type,
                'cost' => $typePrice->cost,
                'currency' => $typePrice->currency ?? 'USD',
            ]);
        }
    }

    /**
     * Handle the Transport "updated" event.
     * If transport type changes, optionally update instance prices.
     */
    public function updated(Transport $transport): void
    {
        // Check if transport_type_id has changed
        if (!$transport->wasChanged('transport_type_id')) {
            return;
        }

        // Only proceed if there are no custom instance prices yet
        // (we don't want to overwrite manually set prices)
        $existingInstancePrices = TransportInstancePrice::where('transport_id', $transport->id)->count();

        if ($existingInstancePrices > 0) {
            // Don't overwrite existing instance prices
            return;
        }

        // New type assigned and no instance prices exist, auto-copy
        if ($transport->transport_type_id) {
            $typePrices = TransportPrice::where('transport_type_id', $transport->transport_type_id)->get();

            foreach ($typePrices as $typePrice) {
                TransportInstancePrice::create([
                    'transport_id' => $transport->id,
                    'price_type' => $typePrice->price_type,
                    'cost' => $typePrice->cost,
                    'currency' => $typePrice->currency ?? 'USD',
                ]);
            }
        }
    }
}
