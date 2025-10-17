<?php

namespace App\Services;

use App\Models\ContractService;
use App\Models\Room;
use App\Models\MealType;
use App\Models\Transport;
use App\Models\TransportPrice;
use App\Models\Monument;
use App\Models\Guide;
use Carbon\Carbon;

class PricingService
{
    /**
     * Get the price for a service, checking contract pricing first, then falling back to base pricing
     */
    public function getPrice(string $serviceType, int $serviceId, ?int $subServiceId = null, ?Carbon $date = null): ?float
    {
        // 1. Try to get contract pricing first
        $contractPrice = $this->getContractPrice($serviceType, $serviceId, $subServiceId, $date);
        
        if ($contractPrice !== null) {
            return $contractPrice;
        }
        
        // 2. Fall back to base pricing
        return $this->getBasePrice($serviceType, $serviceId, $subServiceId);
    }

    /**
     * Get contract pricing for a service
     */
    private function getContractPrice(string $serviceType, int $serviceId, ?int $subServiceId = null, ?Carbon $date = null): ?float
    {
        $date = $date ?? now();

        $query = ContractService::active()
            ->forService($serviceType, $serviceId);

        $contractService = $query->first();

        if (!$contractService) {
            return null;
        }

        // Get the price version active on the given date
        $priceVersion = $contractService->getPriceVersion($date);

        if (!$priceVersion) {
            return null;
        }

        return $this->extractPriceFromPriceVersion($priceVersion, $serviceType, $subServiceId);
    }

    /**
     * Extract price from contract service price version
     */
    private function extractPriceFromPriceVersion($priceVersion, string $serviceType, ?int $subServiceId = null): ?float
    {
        if (!$priceVersion) {
            return null;
        }

        // Handle different pricing structures based on service type
        switch ($serviceType) {
            case 'App\Models\Hotel':
                if ($subServiceId) {
                    // Room-specific pricing
                    return $priceVersion->getPriceForRoom($subServiceId);
                }
                break;

            case 'App\Models\Restaurant':
                if ($subServiceId) {
                    // Meal type-specific pricing
                    return $priceVersion->getPriceForMealType($subServiceId);
                }
                break;

            case 'App\Models\Transport':
            case 'App\Models\Monument':
            case 'App\Models\Guide':
                // Direct pricing
                return $priceVersion->getDirectPrice();
        }

        return null;
    }

    /**
     * Get base pricing for a service
     */
    private function getBasePrice(string $serviceType, int $serviceId, ?int $subServiceId = null): ?float
    {
        switch ($serviceType) {
            case 'App\Models\Hotel':
                if ($subServiceId) {
                    return Room::find($subServiceId)?->cost_per_night;
                }
                return null;

            case 'App\Models\Restaurant':
                if ($subServiceId) {
                    return MealType::find($subServiceId)?->price;
                }
                return null;

            case 'App\Models\Transport':
                // If transport_price_type_id is provided, use transport_prices table
                if ($subServiceId) {
                    return TransportPrice::find($subServiceId)?->cost;
                }
                // Otherwise fall back to transport daily_rate
                return Transport::find($serviceId)?->daily_rate;

            case 'App\Models\Monument':
                return Monument::find($serviceId)?->ticket_price;

            case 'App\Models\Guide':
                return null;

            default:
                return null;
        }
    }

    /**
     * Get pricing breakdown for a booking assignment
     */
    public function getPricingBreakdown(string $serviceType, int $serviceId, ?int $subServiceId = null, ?Carbon $date = null): array
    {
        $contractPrice = $this->getContractPrice($serviceType, $serviceId, $subServiceId, $date);
        $basePrice = $this->getBasePrice($serviceType, $serviceId, $subServiceId);
        
        return [
            'contract_price' => $contractPrice,
            'base_price' => $basePrice,
            'final_price' => $contractPrice ?? $basePrice,
            'has_contract' => $contractPrice !== null,
            'savings' => $contractPrice && $basePrice ? $basePrice - $contractPrice : 0,
            'savings_percentage' => $contractPrice && $basePrice ? round((($basePrice - $contractPrice) / $basePrice) * 100, 2) : 0,
        ];
    }

    /**
     * Get all active contracts for a service
     */
    public function getActiveContracts(string $serviceType, int $serviceId): \Illuminate\Database\Eloquent\Collection
    {
        return ContractService::active()
            ->forService($serviceType, $serviceId)
            ->with('contract.supplier')
            ->get();
    }

    /**
     * Check if a service has active contract pricing
     */
    public function hasActiveContract(string $serviceType, int $serviceId, ?Carbon $date = null): bool
    {
        return $this->getContractPrice($serviceType, $serviceId, null, $date) !== null;
    }

    /**
     * Get contract pricing for multiple services
     */
    public function getBulkPricing(array $services, ?Carbon $date = null): array
    {
        $results = [];
        
        foreach ($services as $service) {
            $serviceType = $service['service_type'];
            $serviceId = $service['service_id'];
            $subServiceId = $service['sub_service_id'] ?? null;
            
            $results[] = [
                'service_type' => $serviceType,
                'service_id' => $serviceId,
                'sub_service_id' => $subServiceId,
                'pricing' => $this->getPricingBreakdown($serviceType, $serviceId, $subServiceId, $date),
            ];
        }
        
        return $results;
    }
}
