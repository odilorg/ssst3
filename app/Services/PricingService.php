<?php

namespace App\Services;

use App\Models\ContractService;
use App\Models\Room;
use App\Models\MealType;
use App\Models\Transport;
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
        $query = ContractService::active()
            ->forService($serviceType, $serviceId);

        if ($date) {
            $query->where(function ($q) use ($date) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $date);
            })->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $date);
            });
        }

        $contractService = $query->first();
        
        if (!$contractService || !$contractService->pricing_structure) {
            return null;
        }

        return $this->extractPriceFromContract($contractService, $subServiceId);
    }

    /**
     * Extract price from contract service pricing structure
     */
    private function extractPriceFromContract(ContractService $contractService, ?int $subServiceId = null): ?float
    {
        $pricingStructure = $contractService->pricing_structure;

        // Handle different pricing structures based on service type
        switch ($contractService->serviceable_type) {
            case 'App\Models\Hotel':
                if ($subServiceId) {
                    // Room-specific pricing
                    return $pricingStructure['rooms'][$subServiceId] ?? null;
                }
                break;

            case 'App\Models\Restaurant':
                if ($subServiceId) {
                    // Meal type-specific pricing
                    return $pricingStructure['meal_types'][$subServiceId] ?? null;
                }
                break;

            case 'App\Models\Transport':
                if ($subServiceId) {
                    // Transport type-specific pricing
                    return $pricingStructure['transport_types'][$subServiceId] ?? null;
                }
                // Direct pricing for transport
                return $pricingStructure['direct_price'] ?? null;

            case 'App\Models\Monument':
                // Direct pricing for monuments
                return $pricingStructure['direct_price'] ?? null;

            case 'App\Models\Guide':
                // Direct pricing for guides
                return $pricingStructure['direct_price'] ?? null;
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
                return Transport::find($serviceId)?->daily_rate;

            case 'App\Models\Monument':
                return Monument::find($serviceId)?->ticket_price;

            case 'App\Models\Guide':
                return Guide::find($serviceId)?->daily_rate;

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
            ->with('contract.supplierCompany')
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
