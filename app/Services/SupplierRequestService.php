<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\SupplierRequest;
use App\Models\Hotel;
use App\Models\Transport;
use App\Models\Guide;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierRequestService
{
    /**
     * Generate supplier requests for a booking
     */
    public function generateRequestsForBooking(Booking $booking)
    {
        $requests = [];
        
        // Get all assignments for this booking
        $assignments = $booking->assignments()
            ->with(['assignable'])
            ->get();
        
        // Group assignments by supplier type
        $groupedAssignments = $assignments->groupBy('assignable_type');
        
        foreach ($groupedAssignments as $assignableType => $typeAssignments) {
            $supplierType = $this->getSupplierType($assignableType);
            
            if (!$supplierType) {
                continue; // Skip monuments and other non-supplier types
            }
            
            foreach ($typeAssignments as $assignment) {
                $supplier = $assignment->assignable;
                if (!$supplier) continue;
                
                $requestData = $this->buildRequestData($booking, $assignment, $supplierType);
                
                // Create supplier request record
                $request = SupplierRequest::createForSupplier(
                    $booking,
                    $supplierType,
                    $supplier->id,
                    $requestData
                );
                
                // Generate PDF
                $pdfPath = $this->generatePDF($request, $supplierType);
                $request->update(['pdf_path' => $pdfPath]);
                
                $requests[] = $request;
            }
        }
        
        return $requests;
    }
    
    /**
     * Get supplier type from assignable type
     */
    private function getSupplierType($assignableType)
    {
        return match($assignableType) {
            Hotel::class => 'hotel',
            Transport::class => 'transport',
            Guide::class => 'guide',
            Restaurant::class => 'restaurant',
            default => null
        };
    }
    
    /**
     * Build request data for a supplier
     */
    public function buildRequestData(Booking $booking, $assignment, $supplierType)
    {
        $baseData = [
            'booking_reference' => $booking->reference,
            'customer_name' => $booking->customer?->name,
            'start_date' => $booking->start_date?->format('d.m.Y'),
            'end_date' => $booking->end_date?->format('d.m.Y'),
            'pax_total' => $booking->pax_total,
            'currency' => $booking->currency ?? 'USD',
            'generated_at' => now()->format('d.m.Y H:i'),
            'expires_at' => now()->addHours(48)->format('d.m.Y H:i'),
        ];
        
        switch ($supplierType) {
            case 'hotel':
                return array_merge($baseData, $this->buildHotelRequestData($booking, $assignment));
            case 'transport':
                return array_merge($baseData, $this->buildTransportRequestData($booking, $assignment));
            case 'guide':
                return array_merge($baseData, $this->buildGuideRequestData($booking, $assignment));
            case 'restaurant':
                return array_merge($baseData, $this->buildRestaurantRequestData($booking, $assignment));
            default:
                return $baseData;
        }
    }
    
    /**
     * Build hotel-specific request data
     */
    private function buildHotelRequestData(Booking $booking, $assignment)
    {
        $hotel = $assignment->assignable;
        $room = $assignment->room_id ? \App\Models\Room::find($assignment->room_id) : null;
        
        return [
            'hotel_name' => $hotel->name,
            'hotel_address' => $hotel->address,
            'room_type' => $room?->name ?? 'Не указан',
            'room_count' => $assignment->quantity ?? 1,
            'check_in' => $booking->start_date?->format('d.m.Y'),
            'check_out' => $booking->end_date?->format('d.m.Y'),
            'nights' => $booking->start_date && $booking->end_date ? 
                $booking->start_date->diffInDays($booking->end_date) : 0,
            'special_requirements' => $assignment->notes ?? 'Нет особых требований',
        ];
    }
    
    /**
     * Build transport-specific request data
     */
    private function buildTransportRequestData(Booking $booking, $assignment)
    {
        $transport = $assignment->assignable;
        $transportType = $transport->transportType;
        
        return [
            'transport_name' => $transportType?->type ?? 'Неизвестный',
            'vehicle_model' => $transport->model ?? 'Не указан',
            'plate_number' => $transport->plate_number ?? 'Не указан',
            'capacity' => $transport->capacity ?? $booking->pax_total,
            'driver_required' => true,
            'special_requirements' => $assignment->notes ?? 'Нет особых требований',
            'usage_dates' => $this->getTransportUsageDates($booking),
        ];
    }
    
    /**
     * Build guide-specific request data
     */
    private function buildGuideRequestData(Booking $booking, $assignment)
    {
        $guide = $assignment->assignable;
        
        return [
            'guide_name' => $guide->name,
            'guide_phone' => $guide->phone,
            'guide_email' => $guide->email,
            'languages' => $guide->spoken_languages ?? ['Русский'],
            'group_size' => $booking->pax_total,
            'tour_dates' => $this->getGuideTourDates($booking),
            'special_requirements' => $assignment->notes ?? 'Нет особых требований',
        ];
    }
    
    /**
     * Build restaurant-specific request data
     */
    private function buildRestaurantRequestData(Booking $booking, $assignment)
    {
        $restaurant = $assignment->assignable;
        $mealType = $assignment->meal_type_id ? \App\Models\MealType::find($assignment->meal_type_id) : null;
        
        return [
            'restaurant_name' => $restaurant->name,
            'restaurant_address' => $restaurant->address,
            'meal_type' => $mealType?->name ?? 'Не указан',
            'meal_time' => $assignment->start_time ?? 'Не указано',
            'group_size' => $booking->pax_total,
            'special_requirements' => $assignment->notes ?? 'Нет особых требований',
            'dietary_requirements' => 'Уточнить при подтверждении',
        ];
    }
    
    /**
     * Get transport usage dates from booking itinerary
     */
    private function getTransportUsageDates(Booking $booking)
    {
        $dates = $booking->itineraryItems()
            ->whereHas('assignments', function($query) {
                $query->where('assignable_type', Transport::class);
            })
            ->pluck('date')
            ->map(function($date) {
                return $date ? $date->format('d.m.Y') : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
            
        return $dates;
    }
    
    /**
     * Get guide tour dates from booking itinerary
     */
    private function getGuideTourDates(Booking $booking)
    {
        $dates = $booking->itineraryItems()
            ->whereHas('assignments', function($query) {
                $query->where('assignable_type', Guide::class);
            })
            ->pluck('date')
            ->map(function($date) {
                return $date ? $date->format('d.m.Y') : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
            
        return $dates;
    }
    
    /**
     * Generate PDF for a supplier request
     */
    public function generatePDF(SupplierRequest $request, $supplierType)
    {
        $template = "supplier-requests.{$supplierType}";
        
        // Get supplier data based on type
        $supplier = $this->getSupplierData($request->supplier_type, $request->supplier_id);
        
        $data = [
            'request' => $request,
            'booking' => $request->booking,
            'supplier' => $supplier,
            'requestData' => $request->request_data,
        ];
        
        $pdf = Pdf::loadView($template, $data);
        $pdf->setPaper('A4', 'portrait');
        
        // Generate unique filename
        $filename = "request_{$request->booking->reference}_{$supplierType}_{$request->supplier_id}_" . now()->format('YmdHis') . '.pdf';
        $path = "supplier-requests/{$request->booking_id}/{$filename}";
        
        // Store PDF
        Storage::disk('public')->put($path, $pdf->output());
        
        return $path;
    }
    
    /**
     * Get supplier data based on type and ID
     */
    private function getSupplierData($supplierType, $supplierId)
    {
        return match($supplierType) {
            'hotel' => Hotel::find($supplierId),
            'transport' => Transport::find($supplierId),
            'guide' => Guide::find($supplierId),
            'restaurant' => Restaurant::find($supplierId),
            default => null,
        };
    }
    
    /**
     * Get download URL for a PDF
     */
    public function getDownloadUrl($pdfPath)
    {
        return Storage::disk('public')->url($pdfPath);
    }
    
    /**
     * Get all requests for a booking
     */
    public function getRequestsForBooking(Booking $booking)
    {
        return SupplierRequest::forBooking($booking->id)
            ->orderBy('supplier_type')
            ->get();
    }
    
    /**
     * Clean up expired requests
     */
    public function cleanupExpiredRequests()
    {
        $expiredRequests = SupplierRequest::getExpiredRequests();
        
        foreach ($expiredRequests as $request) {
            if ($request->pdf_path) {
                Storage::disk('public')->delete($request->pdf_path);
            }
        }
        
        SupplierRequest::markExpiredRequests();
        
        return $expiredRequests->count();
    }
}
