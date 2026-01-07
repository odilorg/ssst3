<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\TourPlatformMapping;
use Illuminate\Console\Command;

class SyncUnmappedBookings extends Command
{
    protected $signature = 'bookings:sync-mappings
                            {--dry-run : Show what would be synced without actually syncing}';

    protected $description = 'Sync unmapped OTA bookings with tour platform mappings';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔍 Searching for unmapped bookings...');
        
        $unmappedBookings = Booking::whereNull('tour_id')->get();
        
        if ($unmappedBookings->isEmpty()) {
            $this->info('✅ No unmapped bookings found.');
            return Command::SUCCESS;
        }
        
        $this->info("Found {$unmappedBookings->count()} unmapped booking(s).\n");
        
        $synced = 0;
        $notFound = 0;
        
        foreach ($unmappedBookings as $booking) {
            $data = $booking->external_platform_data;
            
            if (!$data || !isset($data['external_tour_name'])) {
                $this->warn("⚠️  Booking #" . $booking->id . " (" . $booking->reference . "): No external tour name in data");
                $notFound++;
                continue;
            }
            
            $externalTourName = $data['external_tour_name'];
            
            $mapping = TourPlatformMapping::where('platform', $booking->source)
                                          ->where('external_tour_name', $externalTourName)
                                          ->where('is_active', true)
                                          ->first();
            
            if ($mapping) {
                if ($dryRun) {
                    $this->line("[DRY RUN] Would sync booking #" . $booking->id . " (" . $externalTourName . ") → Tour #" . $mapping->tour_id);
                } else {
                    $booking->tour_id = $mapping->tour_id;
                    $booking->status = 'confirmed';
                    $booking->save();
                    
                    $this->info("✅ Synced booking #" . $booking->id . " (" . $externalTourName . ") → Tour #" . $mapping->tour_id);
                }
                $synced++;
            } else {
                $this->warn("⚠️  No mapping found for booking #" . $booking->id . ": " . $externalTourName);
                $notFound++;
            }
        }
        
        $this->newLine();
        
        if ($dryRun) {
            $this->info("[DRY RUN] Would sync " . $synced . " booking(s), " . $notFound . " not found.");
        } else {
            $this->info("✅ Synced " . $synced . " booking(s), " . $notFound . " not found.");
        }
        
        return Command::SUCCESS;
    }
}
