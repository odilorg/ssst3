<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, create transport instance prices for all existing transports
        $this->createTransportInstancePrices();
        
        // Then, migrate existing assignments
        $this->migrateExistingAssignments();
    }

    /**
     * Create transport instance prices for all existing transports
     */
    private function createTransportInstancePrices(): void
    {
        $transports = DB::table('transports')->get();
        
        foreach ($transports as $transport) {
            // Get transport type prices for this transport
            $transportTypePrices = DB::table('transport_prices')
                ->where('transport_type_id', $transport->transport_type_id)
                ->get();
            
            foreach ($transportTypePrices as $typePrice) {
                // Check if this transport instance price already exists
                $exists = DB::table('transport_instance_prices')
                    ->where('transport_id', $transport->id)
                    ->where('price_type', $typePrice->price_type)
                    ->exists();
                
                if (!$exists) {
                    DB::table('transport_instance_prices')->insert([
                        'transport_id' => $transport->id,
                        'price_type' => $typePrice->price_type,
                        'cost' => $typePrice->cost,
                        'currency' => $typePrice->currency,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Migrate existing transport assignments to use specific transport instances
     */
    private function migrateExistingAssignments(): void
    {
        // Get all transport assignments that currently use transport types
        $assignments = DB::table('booking_itinerary_item_assignments')
            ->where('assignable_type', 'App\\Models\\Transport')
            ->whereNotNull('transport_price_type_id')
            ->get();
        
        foreach ($assignments as $assignment) {
            // Find the transport type price
            $transportTypePrice = DB::table('transport_prices')
                ->where('id', $assignment->transport_price_type_id)
                ->first();
            
            if (!$transportTypePrice) {
                continue;
            }
            
            // Find a transport instance of this type
            $transport = DB::table('transports')
                ->where('transport_type_id', $transportTypePrice->transport_type_id)
                ->first();
            
            if (!$transport) {
                continue;
            }
            
            // Find the corresponding transport instance price
            $instancePrice = DB::table('transport_instance_prices')
                ->where('transport_id', $transport->id)
                ->where('price_type', $transportTypePrice->price_type)
                ->first();
            
            if (!$instancePrice) {
                continue;
            }
            
            // Update the assignment
            DB::table('booking_itinerary_item_assignments')
                ->where('id', $assignment->id)
                ->update([
                    'assignable_id' => $transport->id, // Update to specific transport instance
                    'transport_instance_price_id' => $instancePrice->id,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible
        // We would need to map transport instances back to transport types
        // For now, we'll leave the data as-is
    }
};
