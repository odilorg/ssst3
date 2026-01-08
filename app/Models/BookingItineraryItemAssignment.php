<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingItineraryItemAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "booking_itinerary_item_id",
        "assignable_type",
        "assignable_id",
        "contract_service_id",
        "room_id",
        "meal_type_id",
        "transport_price_type_id",
        "transport_instance_price_id",
        "guide_service_cost",
        "role",
        "quantity",
        "cost",
        "currency",
        "status",
        "start_time",
        "end_time",
        "notes",
    ];

    protected $casts = [
        "quantity" => "integer",
        "cost" => "decimal:2",
    ];

    // Relationships
    public function bookingItineraryItem()
    {
        return $this->belongsTo(BookingItineraryItem::class);
    }

    public function assignable()
    {
        return $this->morphTo();
    }

    public function contractService()
    {
        return $this->belongsTo(ContractService::class);
    }

    public function transportPrice()
    {
        return $this->belongsTo(TransportPrice::class, "transport_price_type_id");
    }

    public function transportInstancePrice()
    {
        return $this->belongsTo(TransportInstancePrice::class, "transport_instance_price_id");
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function mealType()
    {
        return $this->belongsTo(MealType::class);
    }

    /**
     * Get the effective cost for this assignment.
     * Priority: Manual override > Contract price > Individual price
     */
    public function getEffectiveCost(): ?float
    {
        // Level 1: Manual override
        if ($this->cost !== null && (float) $this->cost > 0) {
            return (float) $this->cost;
        }

        // Level 2 & 3: Derive from contract or individual
        return $this->getDerivedCost();
    }

    /**
     * Get the derived cost from the service type (without override).
     */
    public function getDerivedCost(): ?float
    {
        switch ($this->assignable_type) {
            case Guide::class:
                return $this->getGuideCost();

            case Restaurant::class:
                return $this->getRestaurantCost();

            case Hotel::class:
                return $this->getHotelCost();

            case Transport::class:
                return $this->getTransportCost();

            case Monument::class:
                return $this->getMonumentCost();

            default:
                return null;
        }
    }

    /**
     * Get guide cost - checks contract first, then individual pricing.
     */
    protected function getGuideCost(): ?float
    {
        // Level 2: Check contract price first
        if ($this->contract_service_id) {
            $contractPrice = $this->getContractPrice();
            if ($contractPrice !== null) {
                return $contractPrice;
            }
        }

        // Level 3: Fall back to individual pricing
        return $this->getGuideIndividualCost();
    }

    /**
     * Get price from contract service.
     */
    protected function getContractPrice(): ?float
    {
        $contractService = $this->contractService;
        if (!$contractService) {
            return null;
        }

        // Get the booking date to find correct price version
        $bookingDate = $this->bookingItineraryItem?->date;
        
        // Get price version active on booking date
        $priceVersion = $contractService->getPriceVersion($bookingDate);
        if (!$priceVersion) {
            return null;
        }

        // For guides, check for direct_price or daily_rate in price_data
        $priceData = $priceVersion->price_data ?? [];
        
        // Try different price keys
        if (isset($priceData["direct_price"])) {
            return (float) $priceData["direct_price"];
        }
        
        if (isset($priceData["daily_rate"])) {
            return (float) $priceData["daily_rate"];
        }

        // If guide_service_cost index is set, try to find matching price
        if ($this->guide_service_cost !== null && isset($priceData["price_types"])) {
            $index = (int) $this->guide_service_cost;
            if (isset($priceData["price_types"][$index]["price"])) {
                return (float) $priceData["price_types"][$index]["price"];
            }
        }

        return null;
    }

    /**
     * Get guide cost from individual price_types (no contract).
     */
    protected function getGuideIndividualCost(): ?float
    {
        if ($this->guide_service_cost === null) {
            return null;
        }

        $guide = $this->assignable;
        if (!$guide || !$guide->price_types) {
            return null;
        }

        $priceTypes = is_array($guide->price_types) 
            ? $guide->price_types 
            : json_decode($guide->price_types, true);

        $index = (int) $this->guide_service_cost;
        if (isset($priceTypes[$index]["price"])) {
            return (float) $priceTypes[$index]["price"];
        }

        return null;
    }

    /**
     * Get restaurant cost from meal type.
     */
    protected function getRestaurantCost(): ?float
    {
        // Check contract first
        if ($this->contract_service_id) {
            $contractPrice = $this->getRestaurantContractPrice();
            if ($contractPrice !== null) {
                return $contractPrice;
            }
        }

        // Fall back to individual pricing
        if (!$this->meal_type_id) {
            return null;
        }

        $mealType = $this->mealType;
        if (!$mealType) {
            return null;
        }

        $price = (float) $mealType->price;
        $quantity = $this->quantity ?? 1;

        return $price * $quantity;
    }

    /**
     * Get restaurant price from contract.
     */
    protected function getRestaurantContractPrice(): ?float
    {
        if (!$this->meal_type_id) {
            return null;
        }

        $contractService = $this->contractService;
        if (!$contractService) {
            return null;
        }

        $bookingDate = $this->bookingItineraryItem?->date;
        $price = $contractService->getPriceForMealType($this->meal_type_id, $bookingDate);
        
        if ($price !== null) {
            $quantity = $this->quantity ?? 1;
            return $price * $quantity;
        }

        return null;
    }

    /**
     * Get hotel cost from room.
     */
    protected function getHotelCost(): ?float
    {
        // Check contract first
        if ($this->contract_service_id) {
            $contractPrice = $this->getHotelContractPrice();
            if ($contractPrice !== null) {
                return $contractPrice;
            }
        }

        // Fall back to individual pricing
        if (!$this->room_id) {
            return null;
        }

        $room = $this->room;
        if (!$room) {
            return null;
        }

        $price = (float) $room->cost_per_night;
        $quantity = $this->quantity ?? 1;

        return $price * $quantity;
    }

    /**
     * Get hotel price from contract.
     */
    protected function getHotelContractPrice(): ?float
    {
        if (!$this->room_id) {
            return null;
        }

        $contractService = $this->contractService;
        if (!$contractService) {
            return null;
        }

        $bookingDate = $this->bookingItineraryItem?->date;
        $price = $contractService->getPriceForRoom($this->room_id, $bookingDate);
        
        if ($price !== null) {
            $quantity = $this->quantity ?? 1;
            return $price * $quantity;
        }

        return null;
    }

    /**
     * Get transport cost from instance price.
     */
    protected function getTransportCost(): ?float
    {
        if (!$this->transport_instance_price_id) {
            return null;
        }

        $instancePrice = $this->transportInstancePrice;
        if (!$instancePrice) {
            return null;
        }

        return (float) $instancePrice->cost;
    }

    /**
     * Get monument cost (ticket price).
     */
    protected function getMonumentCost(): ?float
    {
        $monument = $this->assignable;
        if (!$monument) {
            return null;
        }

        $price = (float) ($monument->ticket_price ?? 0);
        $quantity = $this->quantity ?? 1;

        return $price * $quantity;
    }

    /**
     * Check if this assignment has a manual cost override.
     */
    public function hasManualCost(): bool
    {
        return $this->cost !== null && (float) $this->cost > 0;
    }

    /**
     * Check if this assignment uses contract pricing.
     */
    public function usesContractPricing(): bool
    {
        return $this->contract_service_id !== null;
    }

    /**
     * Get cost source description for display.
     */
    public function getCostSourceLabel(): string
    {
        if ($this->hasManualCost()) {
            return "Ручной ввод";
        }
        
        if ($this->usesContractPricing()) {
            $contract = $this->contractService?->contract;
            return "Контракт: " . ($contract?->contract_number ?? "N/A");
        }
        
        return "Индивидуальная цена";
    }

    /**
     * Find and set active contract for this assignable (if exists).
     * Called automatically when saving.
     */
    public function resolveContractService(): void
    {
        // Skip if contract already set manually or assignable not set
        if ($this->contract_service_id || !$this->assignable_type || !$this->assignable_id) {
            return;
        }

        // Find active contract for this supplier
        $activeContract = Contract::query()
            ->active()
            ->where("supplier_type", $this->assignable_type)
            ->where("supplier_id", $this->assignable_id)
            ->first();

        if (!$activeContract) {
            return;
        }

        // Find the contract service for this supplier
        $contractService = $activeContract->contractServices()
            ->active()
            ->where("serviceable_type", $this->assignable_type)
            ->where("serviceable_id", $this->assignable_id)
            ->first();

        if ($contractService) {
            $this->contract_service_id = $contractService->id;
        }
    }

    /**
     * Boot method to auto-resolve contract on create.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($assignment) {
            $assignment->resolveContractService();
        });
    }
}
