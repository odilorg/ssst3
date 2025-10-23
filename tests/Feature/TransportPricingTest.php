<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Transport;
use App\Models\TransportType;
use App\Models\TransportPrice;
use App\Models\TransportInstancePrice;
use App\Models\Company;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransportPricingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that transport instance price is used over type price
     */
    public function test_instance_price_overrides_type_price(): void
    {
        // Create transport type with type price
        $company = Company::factory()->create();
        $transportType = TransportType::factory()->create(['type' => 'Sedan']);

        TransportPrice::create([
            'transport_type_id' => $transportType->id,
            'price_type' => 'per_day',
            'cost' => 70.00,
            'currency' => 'USD',
        ]);

        // Create transport with instance price (override)
        $transport = Transport::factory()->create([
            'transport_type_id' => $transportType->id,
            'company_id' => $company->id,
        ]);

        $instancePrice = TransportInstancePrice::create([
            'transport_id' => $transport->id,
            'price_type' => 'per_day',
            'cost' => 150.00, // VIP pricing - overrides type
            'currency' => 'USD',
        ]);

        // Test pricing service
        $pricingService = new PricingService();
        $price = $pricingService->getPrice(
            'App\Models\Transport',
            $transport->id,
            $instancePrice->id
        );

        $this->assertEquals(150.00, $price);
    }

    /**
     * Test that type price is used as fallback when no instance price exists
     */
    public function test_type_price_used_as_fallback(): void
    {
        $company = Company::factory()->create();
        $transportType = TransportType::factory()->create(['type' => 'Sedan']);

        TransportPrice::create([
            'transport_type_id' => $transportType->id,
            'price_type' => 'per_day',
            'cost' => 70.00,
            'currency' => 'USD',
        ]);

        $transport = Transport::factory()->create([
            'transport_type_id' => $transportType->id,
            'company_id' => $company->id,
        ]);

        // Delete auto-created instance prices to test fallback
        TransportInstancePrice::where('transport_id', $transport->id)->delete();

        $pricingService = new PricingService();
        $price = $pricingService->getPrice(
            'App\Models\Transport',
            $transport->id,
            null
        );

        $this->assertEquals(70.00, $price);
    }

    /**
     * Test that new transports auto-copy type prices (Observer)
     */
    public function test_new_transport_auto_copies_type_prices(): void
    {
        $company = Company::factory()->create();
        $transportType = TransportType::factory()->create(['type' => 'Sedan']);

        TransportPrice::create([
            'transport_type_id' => $transportType->id,
            'price_type' => 'per_day',
            'cost' => 70.00,
            'currency' => 'USD',
        ]);

        TransportPrice::create([
            'transport_type_id' => $transportType->id,
            'price_type' => 'per_pickup_dropoff',
            'cost' => 40.00,
            'currency' => 'USD',
        ]);

        // Create new transport - should trigger observer
        $transport = Transport::create([
            'transport_type_id' => $transportType->id,
            'company_id' => $company->id,
            'plate_number' => 'TEST123',
            'model' => 'Test Sedan',
        ]);

        // Check instance prices were auto-created
        $instancePrices = TransportInstancePrice::where('transport_id', $transport->id)->get();

        $this->assertCount(2, $instancePrices);
        $this->assertEquals(70.00, $instancePrices->where('price_type', 'per_day')->first()->cost);
        $this->assertEquals(40.00, $instancePrices->where('price_type', 'per_pickup_dropoff')->first()->cost);
    }
}
