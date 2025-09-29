<?php

namespace App\Http\Controllers\API;

use App\Models\Retreat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class RetreatAPIController extends BaseAPIController
{
    /**
     * Display a listing of available retreats.
     * Only shows retreats starting from current day that are not fully booked.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $retreats = Retreat::with(['bookings' => function($query) {
                    $query->whereIn('is_active', ['confirmed', 'pending']);
                }])
                ->active() // Only active retreats
                ->upcoming() // Starting from current day
                ->orderBy('start_date', 'asc')
                ->get();
            // Filter out fully booked retreats
            $availableRetreats = $retreats->filter(function ($retreat) {
                $bookedSeats = $retreat->bookings->count();
                return $bookedSeats < $retreat->seats;
            });

            // Transform data for API response (basic details only)
            $retreatsList = $availableRetreats->map(function ($retreat) {
                $bookedSeats = $retreat->bookings->count();
                return [
                    'retreat_id' => $retreat->id,
                    'retreat_name' => $retreat->title,
                    'start_date' => $retreat->start_date->format('Y-m-d'),
                    'end_date' => $retreat->end_date->format('Y-m-d'),
                    'available_spots' => $retreat->seats - $bookedSeats,
                    'total_seats' => $retreat->seats,
                    'criteria' => $retreat->criteria,
                    'criteria_label' => $retreat->criteria_label,
                    'is_featured' => (bool) $retreat->is_featured
                ];
            })->values();

            return $this->sendResponse([
                'retreats' => $retreatsList,
                'count' => $retreatsList->count()
            ], 'Available retreats retrieved successfully');
            
        } catch (\Exception $e) {
            \Log::error('API - Failed to retrieve retreats: ' . $e->getMessage());
            return $this->sendServerError('Failed to retrieve retreats');
        }
    }

    /**
     * Display the specified retreat details.
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            // Validate retreat ID
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors(), 'Invalid retreat ID');
            }

            // Find the retreat with bookings
            $retreat = Retreat::with(['bookings' => function($query) {
                    $query->whereIn('is_active', ['confirmed', 'pending']);
                }])
                ->where('id', $id)
                ->active()
                ->first();

            if (!$retreat) {
                return $this->sendNotFound('Retreat not found or inactive');
            }

            // Check if retreat is available (not past and has available spots)
            $bookedSeats = $retreat->bookings->count();
            $availableSpots = $retreat->seats - $bookedSeats;
            $isAvailable = $retreat->start_date->isFuture() && $availableSpots > 0;

            // Prepare detailed response
            $retreatDetails = [
                'retreat_id' => $retreat->id,
                'retreat_name' => $retreat->title,
                'description' => $retreat->description,
                'short_description' => $retreat->short_description,
                'start_date' => $retreat->start_date->format('Y-m-d'),
                'end_date' => $retreat->end_date->format('Y-m-d'),
                'timings' => $retreat->timings,
                // 'location' => [
                //     'name' => $retreat->location,
                //     'address' => $retreat->address,
                //     'city' => $retreat->city,
                //     'state' => $retreat->state,
                //     'country' => $retreat->country,
                //     'postal_code' => $retreat->postal_code,
                //     'coordinates' => [
                //         'latitude' => $retreat->latitude,
                //         'longitude' => $retreat->longitude,
                //     ],
                // ],
                // 'pricing' => [
                //     'price' => (float) $retreat->price,
                //     'discount_price' => $retreat->discount_price ? (float) $retreat->discount_price : null,
                //     'has_discount' => $retreat->discount_price !== null && $retreat->discount_price < $retreat->price,
                //     'discount_percentage' => $retreat->discount_price ? round((($retreat->price - $retreat->discount_price) / $retreat->price) * 100) : 0,
                //     'effective_price' => (float) ($retreat->discount_price ?? $retreat->price),
                // ],
                'availability' => [
                    'total_seats' => $retreat->seats,
                    'booked_seats' => $bookedSeats,
                    'available_spots' => $availableSpots,
                    'is_available' => $isAvailable,
                    'is_fully_booked' => $availableSpots <= 0,
                ],
                'criteria' => [
                    'type' => $retreat->criteria,
                    'label' => $retreat->criteria_label,
                ],
                'details' => [
                    'special_remarks' => $retreat->special_remarks,
                    'instructions' => $retreat->instructions,
                    'is_featured' => (bool) $retreat->is_featured,
                ],
                // 'category' => $retreat->category ? [
                //     'id' => $retreat->category->id,
                //     'name' => $retreat->category->name,
                // ] : null,
                //'featured_image' => $retreat->featured_image_url,
            ];

            return $this->sendResponse($retreatDetails, 'Retreat details retrieved successfully');
            
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::error('API - Failed to retrieve retreat details: ' . $e->getMessage());
            return $this->sendServerError('Failed to retrieve retreat details');
        }
    }
}
