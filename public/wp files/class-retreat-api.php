this adminpanel and the apis are done with phplaravel myswl setup. but i havewebsite based onn wordpress.....how to implement following


This module provides a user-facing interface for booking retreats, similar to the MCRC interface, with Booking ID and WhatsApp integration.
Interface Similarity: Same as MCRC interface, with Booking ID added.
3 pages to be integrated , retreat lists page(https://mountcarmelretreatcentre.org/retreat-booking/) , retreat details popup and book the retreat popup -> Clarification
1 Additional popup to cancel a booking, accept booking number and primary users whatsapp number as verification mechanism->Added to scope
On successful booking , show booking id and link+QR code to join the community via whatsapp->Added to Scope
Provision to view the booking of the user ,accept booking number and primary users whatsapp number as verification mechanism. Select users with checkbox and cancel partially from this. If the user tries to cancel primary user , all checkboxes should be enabled by default->Added to scope
Form Fields and Validations: Identical to Admin form booking (Firstname*, Lastname*, WhatsApp Number*, Age*, Email Address*, Address*, City*, State*, Gender*, Diocese, Parish, Congregation (only for Priests and Sisters), Emergency Contact Person Name*, Emergency Contact Person Phone Number*).
Number of Additional participants (maximum 3) , for each secondary user -> Firstname* (required), Lastname* (required), WhatsApp Number* (required), Age* (required), Email Address* (required),Gender
Field Validations: Name (normal form validation), WhatsApp Number (phone validation), Email Address (format validation), Gender (Male or Female).

Suggest new conversation accepted for message ced8cbc1-054b-4d59-b11e-42beb0d8566d

I understand you have a Laravel-MySQL admin panel/API setup and want to integrate retreat booking functionality into your WordPress website. This is a common integration scenario where you'll need to connect your WordPress frontend to your existing Laravel backend APIs.

Let me first examine your Laravel retreat management system to understand the current API structure and then provide you with a comprehensive integration plan.


**Codebase Search Results:**

- **E:\laragon\www\retreatms\routes\api.php**
```
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RetreatAPIController;
use App\Http\Controllers\API\BookingAPIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Health check endpoint (no authentication required)
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// Protected API routes with authentication
Route::middleware(['api.auth'])->group(function () {
    
    // Retreat endpoints
    Route::prefix('retreats')->group(function () {
        // List available retreats
        Route::get('/', [RetreatAPIController::class, 'index']);
        
        // Get retreat details by ID
        Route::get('/{id}', [RetreatAPIController::class, 'show']);
    });
    
    // Booking endpoints
    Route::prefix('bookings')->group(function () {
        // Create new booking
        Route::post('/', [BookingAPIController::class, 'store']);
        
        // View booking details
        Route::get('/', [BookingAPIController::class, 'show']);
        
        // Partially cancel booking
        Route::patch('/{id}/cancel', [BookingAPIController::class, 'cancel']);
    });
});

```

- **E:\laragon\www\retreatms\app\Http\Controllers\Api\RetreatAPIController.php**
```
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
                    $query->whereIn('status', ['confirmed', 'pending']);
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
                    'location' => $retreat->location,
                    'price' => (float) $retreat->price,
                    'discount_price' => $retreat->discount_price ? (float) $retreat->discount_price : null,
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
                    $query->whereIn('status', ['confirmed', 'pending']);
                }, 'category'])
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
                'location' => [
                    'name' => $retreat->location,
                    'address' => $retreat->address,
                    'city' => $retreat->city,
                    'state' => $retreat->state,
                    'country' => $retreat->country,
                    'postal_code' => $retreat->postal_code,
                    'coordinates' => [
                        'latitude' => $retreat->latitude,
                        'longitude' => $retreat->longitude,
                    ],
                ],
                'pricing' => [
                    'price' => (float) $retreat->price,
                    'discount_price' => $retreat->discount_price ? (float) $retreat->discount_price : null,
                    'has_discount' => $retreat->discount_price !== null && $retreat->discount_price < $retreat->price,
                    'discount_percentage' => $retreat->discount_price ? round((($retreat->price - $retreat->discount_price) / $retreat->price) * 100) : 0,
                    'effective_price' => (float) ($retreat->discount_price ?? $retreat->price),
                ],
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
                'category' => $retreat->category ? [
                    'id' => $retreat->category->id,
                    'name' => $retreat->category->name,
                ] : null,
                'featured_image' => $retreat->featured_image_url,
            ];

            return $this->sendResponse($retreatDetails, 'Retreat details retrieved successfully');
            
        } catch (\Exception $e) {
            \Log::error('API - Failed to retrieve retreat details: ' . $e->getMessage());
            return $this->sendServerError('Failed to retrieve retreat details');
        }
    }
}

```

- **E:\laragon\www\retreatms\app\Http\Requests\RetreatRequest.php**
```
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RetreatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $retreatId = $this->route('retreat');
        $retreatId = $retreatId instanceof \App\Models\Retreat ? $retreatId->id : $retreatId;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('retreats', 'slug')->ignore($retreatId),
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'
            ],
            'description' => ['required', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'timings' => ['required', 'string', 'max:255'],
            'seats' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'location' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'criteria' => [
                'required',
                Rule::in([
                    'male_only',
                    'female_only',
                    'priests_only',
                    'sisters_only',
                    'youth_only',
                    'children',
                    'no_criteria'
                ])
            ],
            'special_remarks' => ['nullable', 'string'],
            'instructions' => ['required', 'string'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048'
            ],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain letters, numbers, and hyphens, and must not begin or end with a hyphen.',
            'end_date.after' => 'The end date must be after the start date.',
            'discount_price.lt' => 'The discount price must be less than the regular price.',
            'featured_image.max' => 'The featured image must not be larger than 2MB.',
            'gallery.*.max' => 'Each image in the gallery must not be larger than 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Generate slug from title if not provided
        if ($this->has('title') && !$this->has('slug')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->title . ' ' . now()->format('Y-m-d'))
            ]);
        }

        // Ensure boolean fields are properly cast
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}

```

- **E:\laragon\www\retreatms\app\Http\Resources\RetreatResource.php**
```
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RetreatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'start_date' => $this->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date->format('Y-m-d H:i:s'),
            'timings' => $this->timings,
            'seats' => $this->seats,
            'available_seats' => $this->seats - $this->bookings()->count(),
            'price' => (float) $this->price,
            'discount_price' => $this->discount_price ? (float) $this->discount_price : null,
            'location' => $this->location,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'coordinates' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'criteria' => $this->criteria,
            'criteria_label' => $this->criteria_label,
            'special_remarks' => $this->special_remarks,
            'instructions' => $this->instructions,
            'is_featured' => (bool) $this->is_featured,
            'is_active' => (bool) $this->is_active,
            'is_available' => $this->is_available,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'featured_image' => $this->getFirstMediaUrl('featured_image'),
            'featured_image_thumb' => $this->getFirstMediaUrl('featured_image', 'thumb'),
            'gallery' => $this->whenLoaded('media', function () {
                return $this->getMedia('gallery')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'thumb_url' => $media->getUrl('thumb'),
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'created_at' => $media->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_by' => new UserResource($this->whenLoaded('creator')),
            'updated_by' => new UserResource($this->whenLoaded('updater')),
        ];
    }

    /**
     * Get any additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'available_seats' => $this->seats - $this->bookings()->count(),
                'total_bookings' => $this->bookings()->count(),
                'is_full' => $this->seats <= $this->bookings()->count(),
                'has_discount' => $this->discount_price !== null && $this->discount_price < $this->price,
                'discount_percentage' => $this->discount_price ? round((($this->price - $this->discount_price) / $this->price) * 100) : 0,
            ],
        ];
    }
}

```

- **E:\laragon\www\retreatms\app\Services\RetreatService.php**
```
<?php

namespace App\Services;

use App\Models\Retreat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class RetreatService
{
    /**
     * Create a new retreat
     *
     * @param array $data
     * @return Retreat
     */
    public function createRetreat(array $data): Retreat
    {
        // Handle featured image
        if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
            $data['featured_image_path'] = $this->storeImage($data['featured_image'], 'retreats/featured');
            unset($data['featured_image']);
        }

        // Set created_by and updated_by
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // Create the retreat
        $retreat = Retreat::create($data);

        // Handle gallery images
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $this->handleGalleryImages($retreat, $data['gallery']);
        }

        return $retreat;
    }

    /**
     * Update an existing retreat
     *
     * @param Retreat $retreat
     * @param array $data
     * @return Retreat
     */
    public function updateRetreat(Retreat $retreat, array $data): Retreat
    {
        // Handle featured image update
        if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
            // Delete old featured image if exists
            if ($retreat->featured_image_path) {
                Storage::delete($retreat->featured_image_path);
            }
            
            $data['featured_image_path'] = $this->storeImage($data['featured_image'], 'retreats/featured');
            unset($data['featured_image']);
        }

        // Set updated_by
        $data['updated_by'] = Auth::id();

        // Update the retreat
        $retreat->update($data);

        // Handle gallery images
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $this->handleGalleryImages($retreat, $data['gallery']);
        }

        return $retreat->fresh();
    }

    /**
     * Delete a retreat and its associated media
     *
     * @param Retreat $retreat
     * @return bool
     */
    public function deleteRetreat(Retreat $retreat): bool
    {
        // Delete featured image if exists
        if ($retreat->featured_image_path) {
            Storage::delete($retreat->featured_image_path);
        }

        // Delete gallery images
        foreach ($retreat->gallery as $image) {
            Storage::delete($image->getPath());
        }

        return $retreat->delete();
    }

    /**
     * Store an uploaded image
     *
     * @param UploadedFile $image
     * @param string $path
     * @return string
     */
    protected function storeImage(UploadedFile $image, string $path): string
    {
        $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
        return $image->storeAs($path, $filename, 'public');
    }

    /**
     * Handle gallery images upload
     *
     * @param Retreat $retreat
     * @param array $images
     * @return void
     */
    protected function handleGalleryImages(Retreat $retreat, array $images): void
    {
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $this->storeImage($image, 'retreats/gallery');
                $retreat->gallery()->create([
                    'path' => $path,
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getClientMimeType(),
                    'size' => $image->getSize(),
                ]);
            }
        }
    }

    /**
     * Get available retreats based on criteria
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableRetreats(array $filters = [])
    {
        $query = Retreat::query()
            ->active()
            ->upcoming()
            ->with(['category', 'media']);

        // Apply filters
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Order by featured first, then by start date
        $query->orderBy('is_featured', 'desc')
              ->orderBy('start_date');

        return $query->get();
    }
}

```





**Codebase Search Results:**

- **E:\laragon\www\retreatms\app\Http\Controllers\Api\BookingAPIController.php**
```
<?php

namespace App\Http\Controllers\API;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Retreat;
use App\Mail\BookingConfirmation;
use App\Mail\BookingCancellation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;

class BookingAPIController extends BaseAPIController
{
    /**
     * Store a newly created booking.
     * Accept JSON array of participant objects.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate request structure
            $initialValidator = Validator::make($request->all(), [
                'retreat_id' => 'required|integer|exists:retreats,id',
                'participants' => 'required|array|min:1|max:4',
            ]);

            if ($initialValidator->fails()) {
                return $this->sendValidationError($initialValidator->errors());
            }

            // Get retreat details
            $retreat = Retreat::with(['bookings' => function($query) {
                $query->active();
            }])->where('id', $request->retreat_id)
              ->active()
              ->first();

            if (!$retreat) {
                return $this->sendNotFound('Retreat not found or inactive');
            }

            // Check if retreat is available for booking
            if ($retreat->start_date->isPast()) {
                return $this->sendError('This retreat has already started or ended', 'RETREAT_PAST');
            }

            $currentBookings = $retreat->bookings->count();
            $requestedSeats = count($request->participants);
            $availableSeats = $retreat->seats - $currentBookings;

            if ($requestedSeats > $availableSeats) {
                return $this->sendError(
                    "Only {$availableSeats} seats available. You requested {$requestedSeats} seats.",
                    'INSUFFICIENT_SEATS'
                );
            }

            // Validate each participant
            $validationErrors = [];
            $participants = $request->participants;

            foreach ($participants as $index => $participant) {
                $participantRules = $this->getParticipantValidationRules($retreat);
                $validator = Validator::make($participant, $participantRules, [
                    'whatsapp_number.digits' => 'WhatsApp number must be exactly 10 digits.',
                    'whatsapp_number.numeric' => 'WhatsApp number must contain only digits.',
                    'congregation.required' => 'Congregation is required for this retreat type.',
                ]);

                if ($validator->fails()) {
                    $validationErrors["participant_{$index}"] = $validator->errors();
                }
            }

            if (!empty($validationErrors)) {
                return $this->sendValidationError($validationErrors, 'Participant validation failed');
            }

            // Check for duplicate participants and business rules
            $businessRuleErrors = $this->validateBusinessRules($participants, $retreat);
            if (!empty($businessRuleErrors)) {
                return $this->sendError('Business rule validation failed', 'BUSINESS_RULES_FAILED', 400, $businessRuleErrors);
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Generate unique booking ID
                $bookingId = Booking::generateBookingId();
                $allBookings = [];
                $primaryBooking = null;

                // Create bookings for all participants
                foreach ($participants as $index => $participantData) {
                    $serialNumber = $index + 1;
                    
                    // Create booking record
                    $booking = Booking::create([
                        'booking_id' => $bookingId,
                        'retreat_id' => $retreat->id,
                        'firstname' => $participantData['firstname'],
                        'lastname' => $participantData['lastname'],
                        'whatsapp_number' => $participantData['whatsapp_number'],
                        'age' => $participantData['age'],
                        'email' => $participantData['email'],
                        'address' => $participantData['address'],
                        'gender' => $participantData['gender'],
                        'city' => $participantData['city'],
                        'state' => $participantData['state'],
                        'diocese' => $participantData['diocese'] ?? null,
                        'parish' => $participantData['parish'] ?? null,
                        'congregation' => $participantData['congregation'] ?? null,
                        'emergency_contact_name' => $participantData['emergency_contact_name'],
                        'emergency_contact_phone' => $participantData['emergency_contact_phone'],
                        'additional_participants' => count($participants) - 1, // For primary participant only
                        'special_remarks' => $participantData['special_remarks'] ?? null,
                        'participant_number' => $serialNumber,
                        'is_active' => true,
                        'created_by' => null, // API bookings don't have user context
                        'updated_by' => null,
                    ]);

                    $allBookings[] = $booking;
                    
                    if ($serialNumber === 1) {
                        $primaryBooking = $booking;
                    }
                }

                // Send confirmation email to primary participant
                if ($primaryBooking) {
                    try {
                        Mail::to($primaryBooking->email)
                            ->send(new BookingConfirmation($primaryBooking, $retreat, $allBookings));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
                        // Don't fail the booking if email fails
                    }
                }

                DB::commit();

                // Prepare response
                $responseData = [
                    'booking_id' => $bookingId,
                    'retreat' => [
                        'id' => $retreat->id,
                        'name' => $retreat->title,
                        'start_date' => $retreat->start_date->format('Y-m-d'),
                        'end_date' => $retreat->end_date->format('Y-m-d'),
                    ],
                    'participants' => collect($allBookings)->map(function ($booking) {
                        return [
                            'serial_number' => $booking->participant_number,
                            'name' => $booking->firstname . ' ' . $booking->lastname,
                            'email' => $booking->email,
                            'whatsapp_number' => $booking->whatsapp_number,
                            'role' => $booking->participant_number === 1 ? 'primary' : 'secondary',
                        ];
                    }),
                    'total_participants' => count($allBookings),
                    'remarks' => 'Booking confirmed successfully. Confirmation email sent to primary participant.',
                ];

                return $this->sendCreated($responseData, 'Booking created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Database error during booking creation: ' . $e->getMessage());
                return $this->sendServerError('Failed to create booking due to database error');
            }

        } catch (\Exception $e) {
            \Log::error('API - Failed to create booking: ' . $e->getMessage());
            return $this->sendServerError('Failed to create booking');
        }
    }

    /**
     * Display the specified booking.
     * Requires booking_id and whatsapp_number for validation.
     */
    public function show(Request $request): JsonResponse
    {
        try {
            // Validate required parameters (can be in headers or query params)
            $bookingId = $request->header('booking-id') ?? $request->query('booking_id');
            $whatsappNumber = $request->header('whatsapp-number') ?? $request->query('whatsapp_number');

            $validator = Validator::make([
                'booking_id' => $bookingId,
                'whatsapp_number' => $whatsappNumber,
            ], [
                'booking_id' => 'required|string',
                'whatsapp_number' => 'required|numeric|digits:10',
            ], [
                'booking_id.required' => 'Booking ID is required in headers or query parameters',
                'whatsapp_number.required' => 'WhatsApp number is required in headers or query parameters',
                'whatsapp_number.digits' => 'WhatsApp number must be exactly 10 digits',
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            // Find the primary booking (participant_number = 1) with the given booking_id and whatsapp_number
            $primaryBooking = Booking::with(['retreat.category'])
                ->where('booking_id', $bookingId)
                ->where('whatsapp_number', $whatsappNumber)
                ->where('participant_number', 1) // Primary participant only
                ->where('is_active', true)
                ->first();

            if (!$primaryBooking) {
                return $this->sendError(
                    'Unable to retrieve booking information for the specified Booking ID and Whatsapp number',
                    'BOOKING_NOT_FOUND',
                    400
                );
            }

            // Get all participants for this booking
            $allParticipants = Booking::where('booking_id', $bookingId)
                ->where('is_active', true)
                ->orderBy('participant_number')
                ->get();

            $retreat = $primaryBooking->retreat;

            // Check if retreat still exists and is active
            if (!$retreat || !$retreat->is_active) {
                return $this->sendError('Associated retreat is no longer available', 'RETREAT_UNAVAILABLE');
            }

            // Calculate booking status
            $now = now();
            $retreatStatus = 'upcoming';
            if ($retreat->end_date->isPast()) {
                $retreatStatus = 'completed';
            } elseif ($retreat->start_date->isPast() && $retreat->end_date->isFuture()) {
                $retreatStatus = 'ongoing';
            }

            // Prepare comprehensive response
            $bookingDetails = [
                'booking_id' => $primaryBooking->booking_id,
                'booking_date' => $primaryBooking->created_at->format('Y-m-d H:i:s'),
                'status' => 'confirmed', // Since we only show active bookings
                'retreat_status' => $retreatStatus,
                
                // Retreat Information
                'retreat' => [
                    'id' => $retreat->id,
                    'name' => $retreat->title,
                    'description' => $retreat->description,
                    'short_description' => $retreat->short_description,
                    'start_date' => $retreat->start_date->format('Y-m-d'),
                    'end_date' => $retreat->end_date->format('Y-m-d'),
                    'start_datetime' => $retreat->start_date->format('Y-m-d H:i:s'),
                    'end_datetime' => $retreat->end_date->format('Y-m-d H:i:s'),
                    'timings' => $retreat->timings,
                    'location' => [
                        'name' => $retreat->location,
                        'address' => $retreat->address,
                        'city' => $retreat->city,
                        'state' => $retreat->state,
                        'country' => $retreat->country,
                        'postal_code' => $retreat->postal_code,
                        'coordinates' => [
                            'latitude' => $retreat->latitude,
                            'longitude' => $retreat->longitude,
                        ],
                    ],
                    'pricing' => [
                        'price' => (float) $retreat->price,
                        'discount_price' => $retreat->discount_price ? (float) $retreat->discount_price : null,
                        'effective_price' => (float) ($retreat->discount_price ?? $retreat->price),
                    ],
                    'criteria' => [
                        'type' => $retreat->criteria,
                        'label' => $retreat->criteria_label,
                    ],
                    'instructions' => $retreat->instructions,
                    'special_remarks' => $retreat->special_remarks,
                    'category' => $retreat->category ? [
                        'id' => $retreat->category->id,
                        'name' => $retreat->category->name,
                    ] : null,
                    'featured_image' => $retreat->featured_image_url,
                ],
                
                // Primary Participant Details
                'primary_participant' => [
                    'serial_number' => $primaryBooking->participant_number,
                    'firstname' => $primaryBooking->firstname,
                    'lastname' => $primaryBooking->lastname,
                    'full_name' => $primaryBooking->firstname . ' ' . $primaryBooking->lastname,
                    'email' => $primaryBooking->email,
                    'whatsapp_number' => $primaryBooking->whatsapp_number,
                    'age' => $primaryBooking->age,
                    'gender' => ucfirst($primaryBooking->gender),
                    'address' => $primaryBooking->address,
                    'city' => $primaryBooking->city,
                    'state' => $primaryBooking->state,
                    'diocese' => $primaryBooking->diocese,
                    'parish' => $primaryBooking->parish,
                    'congregation' => $primaryBooking->congregation,
                    'emergency_contact' => [
                        'name' => $primaryBooking->emergency_contact_name,
                        'phone' => $primaryBooking->emergency_contact_phone,
                    ],
                ],
                
                // All Participants
                'participants' => $allParticipants->map(function ($participant) {
                    return [
                        'serial_number' => $participant->participant_number,
                        'firstname' => $participant->firstname,
                        'lastname' => $participant->lastname,
                        'full_name' => $participant->firstname . ' ' . $participant->lastname,
                        'email' => $participant->email,
                        'whatsapp_number' => $participant->whatsapp_number,
                        'age' => $participant->age,
                        'gender' => ucfirst($participant->gender),
                        'role' => $participant->participant_number === 1 ? 'primary' : 'secondary',
                        'emergency_contact' => [
                            'name' => $participant->emergency_contact_name,
                            'phone' => $participant->emergency_contact_phone,
                        ],
                    ];
                })->values(),
                
                // Booking Summary
                'summary' => [
                    'total_participants' => $allParticipants->count(),
                    'additional_participants' => $primaryBooking->additional_participants,
                    'special_remarks' => $primaryBooking->special_remarks,
                    'booking_flags' => $primaryBooking->flag ? explode(',', $primaryBooking->flag) : [],
                ],
                
                // Important Dates and Status
                'important_info' => [
                    'days_until_retreat' => $retreat->start_date->isFuture() ? 
                        $now->diffInDays($retreat->start_date, false) : null,
                    'is_cancellable' => $retreat->start_date->isFuture() && 
                        $retreat->start_date->diffInDays($now) > 1, // Can cancel if more than 1 day before
                    'check_in_time' => $retreat->start_date->format('M d, Y \a\t g:i A'),
                    'check_out_time' => $retreat->end_date->format('M d, Y \a\t g:i A'),
                    'duration_days' => $retreat->start_date->diffInDays($retreat->end_date) + 1,
                ],
            ];

            return $this->sendResponse($bookingDetails, 'Booking details retrieved successfully');
            
        } catch (\Exception $e) {
            \Log::error('API - Failed to retrieve booking details: ' . $e->getMessage());
            return $this->sendServerError('Failed to retrieve booking details');
        }
    }

    /**
     * Partially cancel booking.
     * Cancel specific participant by their serial number.
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            // Validate input parameters
            $validator = Validator::make(array_merge($request->all(), ['booking_id' => $id]), [
                'booking_id' => 'required|string',
                'serial_number' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            $bookingId = $id;
            $serialNumber = $request->input('serial_number');

            // Find the specific participant to cancel
            $participantToCancel = Booking::where('booking_id', $bookingId)
                ->where('participant_number', $serialNumber)
                ->where('is_active', true)
                ->first();

            if (!$participantToCancel) {
                return $this->sendError(
                    'Unable to cancel booking for the specified Booking ID and participant serial number',
                    'PARTICIPANT_NOT_FOUND',
                    400
                );
            }

            // Get retreat details
            $retreat = $participantToCancel->retreat;
            if (!$retreat) {
                return $this->sendError('Associated retreat not found', 'RETREAT_NOT_FOUND');
            }

            // Check if retreat is cancellable (not already started)
            if ($retreat->start_date->isPast()) {
                return $this->sendError(
                    'Cannot cancel booking for a retreat that has already started',
                    'RETREAT_ALREADY_STARTED'
                );
            }

            // Get all participants for this booking
            $allParticipants = Booking::where('booking_id', $bookingId)
                ->where('is_active', true)
                ->orderBy('participant_number')
                ->get();

            if ($allParticipants->isEmpty()) {
                return $this->sendError('No active participants found for this booking', 'NO_PARTICIPANTS');
            }

            // Check if this is the last participant (would result in complete cancellation)
            if ($allParticipants->count() === 1) {
                // This is a complete cancellation
                return $this->processCompleteCancellation($participantToCancel, $retreat, $allParticipants);
            }

            // Process partial cancellation
            DB::beginTransaction();

            try {
                // Mark participant as inactive (soft cancel)
                $participantToCancel->update([
                    'is_active' => false,
                    'updated_by' => null, // API cancellations don't have user context
                ]);

                // Get remaining active participants
                $remainingParticipants = $allParticipants->where('id', '!=', $participantToCancel->id);

                // Get primary booking for email (participant_number = 1)
                $primaryBooking = $remainingParticipants->where('participant_number', 1)->first() ?:
                                 $allParticipants->where('participant_number', 1)->first();

                if (!$primaryBooking) {
                    throw new \Exception('Primary booking not found');
                }

                // Update additional_participants count for primary booking
                if ($primaryBooking && $primaryBooking->is_active) {
                    $activeCount = $remainingParticipants->count();
                    $primaryBooking->update([
                        'additional_participants' => max(0, $activeCount - 1), // Subtract 1 for primary
                    ]);
                }

                // Send cancellation confirmation email to primary participant
                try {
                    Mail::to($primaryBooking->email)
                        ->send(new BookingCancellation(
                            $primaryBooking,
                            $retreat,
                            $participantToCancel,
                            $remainingParticipants->values(),
                            'partial'
                        ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send cancellation confirmation email: ' . $e->getMessage());
                    // Don't fail the cancellation if email fails
                }

                DB::commit();

                // Prepare response
                $responseData = [
                    'booking_id' => $bookingId,
                    'cancelled_participant' => [
                        'serial_number' => $participantToCancel->participant_number,
                        'name' => $participantToCancel->firstname . ' ' . $participantToCancel->lastname,
                        'email' => $participantToCancel->email,
                        'whatsapp_number' => $participantToCancel->whatsapp_number,
                    ],
                    'remaining_participants' => $remainingParticipants->map(function ($participant) {
                        return [
                            'serial_number' => $participant->participant_number,
                            'name' => $participant->firstname . ' ' . $participant->lastname,
                            'email' => $participant->email,
                            'whatsapp_number' => $participant->whatsapp_number,
                            'role' => $participant->participant_number === 1 ? 'primary' : 'secondary',
                        ];
                    })->values(),
                    'retreat' => [
                        'id' => $retreat->id,
                        'name' => $retreat->title,
                        'start_date' => $retreat->start_date->format('Y-m-d'),
                        'end_date' => $retreat->end_date->format('Y-m-d'),
                    ],
                    'cancellation_type' => 'partial',
                    'total_remaining' => $remainingParticipants->count(),
                    'message' => 'Participant cancelled successfully. Confirmation email sent to primary participant.',
                ];

                return $this->sendResponse($responseData, 'Booking participant cancelled successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Database error during partial cancellation: ' . $e->getMessage());
                return $this->sendServerError('Failed to cancel participant due to database error');
            }

        } catch (\Exception $e) {
            \Log::error('API - Failed to cancel booking participant: ' . $e->getMessage());
            return $this->sendServerError('Failed to cancel booking participant');
        }
    }

    /**
     * Get validation rules for each participant based on retreat criteria.
     */
    private function getParticipantValidationRules(Retreat $retreat): array
    {
        // Base validation rules for all participants
        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'whatsapp_number' => 'required|numeric|digits:10',
            'age' => 'required|integer|min:1|max:120',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'gender' => 'required|in:male,female,other',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'diocese' => 'nullable|string|max:255',
            'parish' => 'nullable|string|max:255',
            'congregation' => 'nullable|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'special_remarks' => 'nullable|string|max:1000',
        ];

        // Add specific rules based on retreat criteria
        if (in_array($retreat->criteria, ['priests_only', 'sisters_only'])) {
            $rules['congregation'] = 'required|string|max:255';
        }

        return $rules;
    }

    /**
     * Validate business rules for participants.
     */
    private function validateBusinessRules(array $participants, Retreat $retreat): array
    {
        $errors = [];

        // Check for duplicate WhatsApp numbers within the same booking
        $whatsappNumbers = collect($participants)->pluck('whatsapp_number');
        $duplicates = $whatsappNumbers->duplicates();
        
        if ($duplicates->isNotEmpty()) {
            $errors[] = 'Duplicate WhatsApp numbers found within participants: ' . $duplicates->implode(', ');
        }

        // Check for duplicate email addresses within the same booking
        $emails = collect($participants)->pluck('email');
        $duplicateEmails = $emails->duplicates();
        
        if ($duplicateEmails->isNotEmpty()) {
            $errors[] = 'Duplicate email addresses found within participants: ' . $duplicateEmails->implode(', ');
        }

        // Validate each participant against retreat criteria and recurrent booking rules
        foreach ($participants as $index => $participant) {
            $participantPosition = $index + 1;
            
            // Check retreat criteria compliance
            if (!$this->meetsRetreatCriteria($participant, $retreat)) {
                $criteriaLabel = $retreat->criteria_label ?? $retreat->criteria;
                $errors[] = "Participant {$participantPosition} ({$participant['firstname']} {$participant['lastname']}) does not meet retreat criteria: {$criteriaLabel}";
            }

            // Check for recurrent bookings (within past year)
            $hasRecentBooking = Booking::hasAttendedInPastYear(
                $participant['whatsapp_number'],
                $participant['firstname'],
                $participant['lastname']
            );

            if ($hasRecentBooking) {
                $errors[] = "Participant {$participantPosition} ({$participant['firstname']} {$participant['lastname']}) has attended a retreat in the past year";
            }
        }

        return $errors;
    }

    /**
     * Check if participant meets retreat criteria.
     */
    private function meetsRetreatCriteria(array $participant, Retreat $retreat): bool
    {
        if ($retreat->criteria === 'no_criteria') {
            return true;
        }

        $criteriaCheck = [
            'male_only' => $participant['gender'] === 'male',
            'female_only' => $participant['gender'] === 'female',
            'priests_only' => !empty($participant['congregation']),
            'sisters_only' => $participant['gender'] === 'female' && !empty($participant['congregation']),
            'youth_only' => $participant['age'] >= 16 && $participant['age'] <= 30,
            'children' => $participant['age'] <= 15,
        ];

        return $criteriaCheck[$retreat->criteria] ?? false;
    }

    /**
     * Process complete cancellation when last participant is cancelled.
     */
    private function processCompleteCancellation($participantToCancel, $retreat, $allParticipants): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Mark all participants as inactive
            Booking::where('booking_id', $participantToCancel->booking_id)
                ->update([
                    'is_active' => false,
                    'updated_by' => null,
                ]);

            // Get primary booking for email
            $primaryBooking = $allParticipants->where('participant_number', 1)->first();
            if (!$primaryBooking) {
                $primaryBooking = $participantToCancel; // Fallback if primary not found
            }

            // Send complete cancellation email
            try {
                Mail::to($primaryBooking->email)
                    ->send(new BookingCancellation(
                        $primaryBooking,
                        $retreat,
                        $participantToCancel,
                        collect(), // No remaining participants
                        'full'
                    ));
            } catch (\Exception $e) {
                \Log::error('Failed to send complete cancellation email: ' . $e->getMessage());
                // Don't fail the cancellation if email fails
            }

            DB::commit();

            // Prepare response for complete cancellation
            $responseData = [
                'booking_id' => $participantToCancel->booking_id,
                'cancelled_participant' => [
                    'serial_number' => $participantToCancel->participant_number,
                    'name' => $participantToCancel->firstname . ' ' . $participantToCancel->lastname,
                    'email' => $participantToCancel->email,
                    'whatsapp_number' => $participantToCancel->whatsapp_number,
                ],
                'remaining_participants' => [],
                'retreat' => [
                    'id' => $retreat->id,
                    'name' => $retreat->title,
                    'start_date' => $retreat->start_date->format('Y-m-d'),
                    'end_date' => $retreat->end_date->format('Y-m-d'),
                ],
                'cancellation_type' => 'complete',
                'total_remaining' => 0,
                'message' => 'Complete booking cancelled successfully. All participants have been cancelled.',
            ];

            return $this->sendResponse($responseData, 'Complete booking cancelled successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Database error during complete cancellation: ' . $e->getMessage());
            return $this->sendServerError('Failed to cancel complete booking due to database error');
        }
    }
}

```

- **E:\laragon\www\retreatms\app\Http\Requests\BookingRequest.php**
```
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxAdditionalMembers = config('bookings.max_additional_members', 3);
        
        // Get the selected retreat to check criteria
        $retreat = null;
        if ($this->has('retreat_id')) {
            $retreat = \App\Models\Retreat::find($this->input('retreat_id'));
        }
        
        // Determine if congregation is required based on retreat criteria
        $congregationRule = 'nullable|string|max:255';
        if ($retreat && in_array($retreat->criteria, ['priests_only', 'sisters_only'])) {
            $congregationRule = 'required|string|max:255';
        }
        
        $rules = [
            'retreat_id' => 'required|exists:retreats,id',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|min:10|max:10|regex:/^[0-9]+$/',
            'age' => 'required|integer|min:1|max:120',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'gender' => 'required|in:male,female,other',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'diocese' => 'nullable|string|max:255',
            'parish' => 'nullable|string|max:255',
            'congregation' => $congregationRule,
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'additional_participants' => 'required|integer|min:0|max:' . $maxAdditionalMembers,
            'special_remarks' => 'nullable|string',
        ];

        // Add validation for additional participants if any
        $additionalParticipants = (int) $this->input('additional_participants', 0);
        
        if ($additionalParticipants > 0) {
            $rules['participants'] = 'required|array|min:1|max:' . $maxAdditionalMembers;
            
            foreach ($this->input('participants', []) as $index => $participant) {
                $rules["participants.{$index}.firstname"] = 'required|string|max:255';
                $rules["participants.{$index}.lastname"] = 'required|string|max:255';
                $rules["participants.{$index}.whatsapp_number"] = 'required|string|min:10|max:10|regex:/^[0-9]+$/';
                $rules["participants.{$index}.age"] = 'required|integer|min:1|max:120';
                $rules["participants.{$index}.email"] = 'required|email|max:255';
                $rules["participants.{$index}.gender"] = 'required|in:male,female,other';
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'congregation.required' => 'The congregation field is required for Priests and Sisters retreats.',
            'whatsapp_number.min' => 'WhatsApp number must be exactly 10 digits.',
            'whatsapp_number.max' => 'WhatsApp number must be exactly 10 digits.',
            'whatsapp_number.regex' => 'WhatsApp number must contain only digits.',
            'participants.*.whatsapp_number.min' => 'WhatsApp number must be exactly 10 digits.',
            'participants.*.whatsapp_number.max' => 'WhatsApp number must be exactly 10 digits.',
            'participants.*.whatsapp_number.regex' => 'WhatsApp number must contain only digits.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [
            'participants.*.firstname' => 'first name',
            'participants.*.lastname' => 'last name',
            'participants.*.whatsapp_number' => 'WhatsApp number',
            'participants.*.age' => 'age',
            'participants.*.email' => 'email',
            'participants.*.gender' => 'gender',
        ];

        return $attributes;
    }
}

```

- **E:\laragon\www\retreatms\app\Models\Booking.php**
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{

    protected $fillable = [
        'booking_id',
        'retreat_id',
        'firstname',
        'lastname',
        'whatsapp_number',
        'age',
        'email',
        'address',
        'gender',
        'city',
        'state',
        'diocese',
        'parish',
        'congregation',
        'emergency_contact_name',
        'emergency_contact_phone',
        'additional_participants',
        'special_remarks',
        'flag',
        'participant_number',
        'created_by',
        'updated_by',
        'is_active',
    ];

    protected $casts = [
        'age' => 'integer',
        'additional_participants' => 'integer',
        'participant_number' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the retreat that owns the booking.
     */
    public function retreat(): BelongsTo
    {
        return $this->belongsTo(Retreat::class);
    }

    /**
     * Get the user who created the booking.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the booking.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all active bookings with the same booking ID (primary + additional participants).
     */
    public function allParticipants()
    {
        return self::where('booking_id', $this->booking_id)
            ->where('is_active', true)
            ->orderBy('participant_number')
            ->get();
    }

    /**
     * Generate the next booking ID (RB1, RB2, etc.).
     */
    public static function generateBookingId(): string
    {
        $lastBooking = self::where('booking_id', 'like', 'RB%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastBooking) {
            return 'RB1';
        }

        $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastBooking->booking_id);
        return 'RB' . ($lastNumber + 1);
    }
    
    /**
     * Scope to get only active bookings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the user has attended a retreat in the past year based on exact match.
     * Checks combination of firstname, lastname, whatsapp_number within the past year.
     * Excludes the current booking if booking ID is provided.
     */
    public static function hasAttendedInPastYear(string $whatsappNumber, string $firstName, string $lastName, string $currentBookingId = null): bool
    {
        $query = self::where('whatsapp_number', $whatsappNumber)
            ->where('firstname', $firstName)
            ->where('lastname', $lastName)
            ->where('is_active', true)
            ->where('created_at', '>=', now()->subYear());
        
        // Exclude the current booking if provided (for updates)
        if ($currentBookingId) {
            $query->where('booking_id', '!=', $currentBookingId);
        }
        
        return $query->exists();
    }

    /**
     * Check if user meets retreat criteria.
     */
    public function meetsRetreatCriteria(): bool
    {
        $retreat = $this->retreat;
        
        if (!$retreat || $retreat->criteria === 'no_criteria') {
            return true;
        }

        $criteriaCheck = [
            'male_only' => $this->gender === 'male',
            'female_only' => $this->gender === 'female',
            'priests_only' => $this->congregation !== null && !empty(trim($this->congregation)),
            'sisters_only' => $this->gender === 'female' && $this->congregation !== null && !empty(trim($this->congregation)),
            'youth_only' => $this->age >= 16 && $this->age <= 30, // Youth: Age 16-30
            'children' => $this->age <= 15, // Children: Age 15 or below
        ];

        return $criteriaCheck[$retreat->criteria] ?? false;
    }
}

```



Now let me check for any authentication middleware to understand the API security setup:


**Codebase Search Results:**

- **E:\laragon\www\retreatms\config\auth.php**
```
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];

```

- **E:\laragon\www\retreatms\app\Http\Middleware\TrackUserActivity.php**
```
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);
        }

        return $next($request);
    }
}

```

- **E:\laragon\www\retreatms\app\Providers\AuthServiceProvider.php**
```
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        \App\Models\Retreat::class => \App\Policies\RetreatPolicy::class,
        \App\Models\Booking::class => \App\Policies\BookingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define a before callback that runs before all other authorization checks
        Gate::before(function (User $user, $ability) {
            // If user is a super admin, authorize all actions
            if ($user->role && $user->role->is_super_admin) {
                return true;
            }
        });

        // Define gates for all permissions
        $this->defineGates();
    }

    /**
     * Define gates for all permissions in the database
     */
    protected function defineGates(): void
    {
        try {
            // Get all permissions from the database
            $permissions = \App\Models\Permission::all();
            
            foreach ($permissions as $permission) {
                Gate::define($permission->slug, function (User $user) use ($permission) {
                    return $user->hasPermission($permission->slug);
                });
            }
        } catch (\Exception $e) {
            // In case the permissions table doesn't exist yet (during migrations)
            return;
        }
    }
}

```

- **E:\laragon\www\retreatms\app\Http\Controllers\Admin\Auth\LoginController.php**
```
<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the admin login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

```

- **E:\laragon\www\retreatms\app\Http\Middleware\ApiAuthentication.php**
```
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for API key in headers
        $apiKey = $request->header('RETREAT-API-KEY') ?? $request->header('Authorization');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required',
                'error_code' => 'MISSING_API_KEY'
            ], 401);
        }
        
        // Remove 'Bearer ' prefix if present
        $apiKey = str_replace('Bearer ', '', $apiKey);
        
        // Validate API key (you can store this in config or database)
        $validApiKey = config('app.api_key', 'retreat_api_key_2024');
        
        if ($apiKey !== $validApiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
                'error_code' => 'INVALID_API_KEY'
            ], 401);
        }
        
        // Generate or retrieve session ID
        $sessionId = $request->header('X-Session-ID');
        
        if (!$sessionId) {
            $sessionId = 'api_session_' . Str::uuid();
            
            // Store session in cache for 24 hours
            Cache::put("api_session:{$sessionId}", [
                'created_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_activity' => now()
            ], 60 * 24); // 24 hours
        } else {
            // Update last activity for existing session
            $sessionData = Cache::get("api_session:{$sessionId}");
            if ($sessionData) {
                $sessionData['last_activity'] = now();
                Cache::put("api_session:{$sessionId}", $sessionData, 60 * 24);
            }
        }
        
        // Add session ID to request
        $request->merge(['session_id' => $sessionId]);
        
        // Add session ID to response headers
        $response = $next($request);
        
        if (method_exists($response, 'header')) {
            $response->header('X-Session-ID', $sessionId);
        }
        
        return $response;
    }
}

```

- **E:\laragon\www\retreatms\routes\api.php**
```
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RetreatAPIController;
use App\Http\Controllers\API\BookingAPIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Health check endpoint (no authentication required)
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// Protected API routes with authentication
Route::middleware(['api.auth'])->group(function () {
    
    // Retreat endpoints
    Route::prefix('retreats')->group(function () {
        // List available retreats
        Route::get('/', [RetreatAPIController::class, 'index']);
        
        // Get retreat details by ID
        Route::get('/{id}', [RetreatAPIController::class, 'show']);
    });
    
    // Booking endpoints
    Route::prefix('bookings')->group(function () {
        // Create new booking
        Route::post('/', [BookingAPIController::class, 'store']);
        
        // View booking details
        Route::get('/', [BookingAPIController::class, 'show']);
        
        // Partially cancel booking
        Route::patch('/{id}/cancel', [BookingAPIController::class, 'cancel']);
    });
});

```



Perfect! Now I have a comprehensive understanding of your Laravel retreat management system. Let me create a complete integration plan for WordPress. 
UpdateTodos: 10 items
Based on your Laravel retreat management system analysis, I'll help you integrate the retreat booking functionality into your WordPress website. Here's my comprehensive approach:

## Integration Overview

Your Laravel system has a well-structured API with:
- **Authentication**: API key-based authentication (`RETREAT-API-KEY` header)
- **Retreat Endpoints**: List and detail views
- **Booking Endpoints**: Create, view, and cancel bookings
- **Validation**: Comprehensive form validation matching your requirements

Let me start building the WordPress integration:


**Diff:**
```diff
--- E:\laragon\www\knk\wp-content\themes\your-theme\includes\class-retreat-api.php
+++ E:\laragon\www\knk\wp-content\themes\your-theme\includes\class-retreat-api.php
@@ -0,0 +1,244 @@
+<?php
+/**
+ * Retreat API Integration Class
+ * Handles communication between WordPress and Laravel Retreat Management System
+ */
+
+class RetreatAPI {
+    
+    private $api_base_url;
+    private $api_key;
+    private $session_id;
+    
+    public function __construct() {
+        // Configure these in your WordPress admin or wp-config.php
+        $this->api_base_url = defined('RETREAT_API_URL') ? RETREAT_API_URL : 'http://localhost/retreatms/api';
+        $this->api_key = defined('RETREAT_API_KEY') ? RETREAT_API_KEY : 'retreat_api_key_2024';
+        $this->session_id = $this->get_session_id();
+    }
+    
+    /**
+     * Get or create session ID for API calls
+     */
+    private function get_session_id() {
+        $session_id = get_transient('retreat_api_session_id');
+        if (!$session_id) {
+            $session_id = 'wp_session_' . wp_generate_uuid4();
+            set_transient('retreat_api_session_id', $session_id, DAY_IN_SECONDS);
+        }
+        return $session_id;
+    }
+    
+    /**
+     * Make API request with proper headers
+     */
+    private function make_request($endpoint, $method = 'GET', $data = null) {
+        $url = trailingslashit($this->api_base_url) . ltrim($endpoint, '/');
+        
+        $headers = [
+            'RETREAT-API-KEY' => $this->api_key,
+            'X-Session-ID' => $this->session_id,
+            'Content-Type' => 'application/json',
+            'Accept' => 'application/json'
+        ];
+        
+        $args = [
+            'method' => $method,
+            'headers' => $headers,
+            'timeout' => 30,
+            'sslverify' => false // Set to true in production
+        ];
+        
+        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
+            $args['body'] = json_encode($data);
+        }
+        
+        $response = wp_remote_request($url, $args);
+        
+        if (is_wp_error($response)) {
+            return [
+                'success' => false,
+                'message' => 'API connection failed: ' . $response->get_error_message(),
+                'error_code' => 'CONNECTION_ERROR'
+            ];
+        }
+        
+        $status_code = wp_remote_retrieve_response_code($response);
+        $body = wp_remote_retrieve_body($response);
+        $decoded_body = json_decode($body, true);
+        
+        // Update session ID if provided in response
+        $response_headers = wp_remote_retrieve_headers($response);
+        if (isset($response_headers['x-session-id'])) {
+            $this->session_id = $response_headers['x-session-id'];
+            set_transient('retreat_api_session_id', $this->session_id, DAY_IN_SECONDS);
+        }
+        
+        return [
+            'success' => $status_code >= 200 && $status_code < 300,
+            'status_code' => $status_code,
+            'data' => $decoded_body,
+            'raw_body' => $body
+        ];
+    }
+    
+    /**
+     * Get list of available retreats
+     */
+    public function get_retreats($filters = []) {
+        $endpoint = 'retreats';
+        if (!empty($filters)) {
+            $endpoint .= '?' . http_build_query($filters);
+        }
+        
+        $response = $this->make_request($endpoint);
+        
+        if ($response['success'] && isset($response['data']['data'])) {
+            return [
+                'success' => true,
+                'retreats' => $response['data']['data']['retreats'],
+                'count' => $response['data']['data']['count']
+            ];
+        }
+        
+        return [
+            'success' => false,
+            'message' => $response['data']['message'] ?? 'Failed to fetch retreats',
+            'error_code' => $response['data']['error_code'] ?? 'FETCH_ERROR'
+        ];
+    }
+    
+    /**
+     * Get retreat details by ID
+     */
+    public function get_retreat($retreat_id) {
+        $response = $this->make_request("retreats/{$retreat_id}");
+        
+        if ($response['success'] && isset($response['data']['data'])) {
+            return [
+                'success' => true,
+                'retreat' => $response['data']['data']
+            ];
+        }
+        
+        return [
+            'success' => false,
+            'message' => $response['data']['message'] ?? 'Retreat not found',
+            'error_code' => $response['data']['error_code'] ?? 'RETREAT_NOT_FOUND'
+        ];
+    }
+    
+    /**
+     * Create a new booking
+     */
+    public function create_booking($booking_data) {
+        $response = $this->make_request('bookings', 'POST', $booking_data);
+        
+        if ($response['success'] && isset($response['data']['data'])) {
+            return [
+                'success' => true,
+                'booking' => $response['data']['data']
+            ];
+        }
+        
+        return [
+            'success' => false,
+            'message' => $response['data']['message'] ?? 'Booking failed',
+            'error_code' => $response['data']['error_code'] ?? 'BOOKING_ERROR',
+            'errors' => $response['data']['errors'] ?? [],
+            'status_code' => $response['status_code']
+        ];
+    }
+    
+    /**
+     * Get booking details
+     */
+    public function get_booking($booking_id, $whatsapp_number) {
+        $response = $this->make_request("bookings?booking_id={$booking_id}&whatsapp_number={$whatsapp_number}");
+        
+        if ($response['success'] && isset($response['data']['data'])) {
+            return [
+                'success' => true,
+                'booking' => $response['data']['data']
+            ];
+        }
+        
+        return [
+            'success' => false,
+            'message' => $response['data']['message'] ?? 'Booking not found',
+            'error_code' => $response['data']['error_code'] ?? 'BOOKING_NOT_FOUND'
+        ];
+    }
+    
+    /**
+     * Cancel booking participant
+     */
+    public function cancel_booking($booking_id, $serial_number) {
+        $response = $this->make_request("bookings/{$booking_id}/cancel", 'PATCH', [
+            'serial_number' => $serial_number
+        ]);
+        
+        if ($response['success'] && isset($response['data']['data'])) {
+            return [
+                'success' => true,
+                'cancellation' => $response['data']['data']
+            ];
+        }
+        
+        return [
+            'success' => false,
+            'message' => $response['data']['message'] ?? 'Cancellation failed',
+            'error_code' => $response['data']['error_code'] ?? 'CANCELLATION_ERROR'
+        ];
+    }
+    
+    /**
+     * Test API connection
+     */
+    public function test_connection() {
+        $response = $this->make_request('health');
+        return $response['success'] && isset($response['data']['success']) && $response['data']['success'] === true;
+    }
+    
+    /**
+     * Validate phone number format
+     */
+    public static function validate_phone($phone) {
+        $phone = preg_replace('/[^0-9]/', '', $phone);
+        return strlen($phone) === 10 && is_numeric($phone);
+    }
+    
+    /**
+     * Generate WhatsApp community join URL
+     */
+    public static function generate_whatsapp_url($message = '') {
+        // Replace with your actual WhatsApp community/group link
+        $base_url = 'https://chat.whatsapp.com/your_community_invite_link';
+        
+        if ($message) {
+            $base_url .= '?text=' . urlencode($message);
+        }
+        
+        return $base_url;
+    }
+    
+    /**
+     * Generate QR code for WhatsApp link
+     */
+    public static function generate_qr_code($url, $size = 200) {
+        // Using Google Charts API for QR code generation
+        $qr_url = "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl=" . urlencode($url);
+        return $qr_url;
+    }
+}
+
+// Initialize the API class
+if (!function_exists('retreat_api')) {
+    function retreat_api() {
+        static $instance = null;
+        if ($instance === null) {
+            $instance = new RetreatAPI();
+        }
+        return $instance;
+    }
+}

```



MarkAsCompleted: 1 items
Now let me create the retreat listing template:
