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
        $apiKey = $request->header('X-API-Key') ?? $request->header('Authorization');
        
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
- **Authentication**: API key-based authentication (`X-API-Key` header)
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
+            'X-API-Key' => $this->api_key,
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


**Diff:**
```diff
--- E:\laragon\www\knk\wp-content\themes\your-theme\template-retreat-booking.php
+++ E:\laragon\www\knk\wp-content\themes\your-theme\template-retreat-booking.php
@@ -0,0 +1,477 @@
+<?php
+/**
+ * Template Name: Retreat Booking
+ * Description: Template for displaying retreat listings and booking interface
+ */
+
+// Include the API class
+require_once get_template_directory() . '/includes/class-retreat-api.php';
+
+get_header(); 
+?>
+
+<div id="retreat-booking-container" class="retreat-booking-page">
+    <div class="container">
+        <div class="row">
+            <div class="col-12">
+                <!-- Page Header -->
+                <div class="retreat-page-header">
+                    <h1><?php the_title(); ?></h1>
+                    <p class="lead">Book your spiritual retreat with Mount Carmel Retreat Centre</p>
+                </div>
+
+                <!-- Loading Indicator -->
+                <div id="retreat-loading" class="loading-indicator" style="display: none;">
+                    <div class="spinner"></div>
+                    <p>Loading retreats...</p>
+                </div>
+
+                <!-- Error Message -->
+                <div id="retreat-error-message" class="alert alert-danger" style="display: none;"></div>
+
+                <!-- Filters Section -->
+                <div class="retreat-filters">
+                    <div class="row">
+                        <div class="col-md-3">
+                            <div class="filter-group">
+                                <label for="date-filter">Date Range</label>
+                                <select id="date-filter" class="form-control">
+                                    <option value="">All Dates</option>
+                                    <option value="this-month">This Month</option>
+                                    <option value="next-month">Next Month</option>
+                                    <option value="next-3-months">Next 3 Months</option>
+                                </select>
+                            </div>
+                        </div>
+                        <div class="col-md-3">
+                            <div class="filter-group">
+                                <label for="criteria-filter">Retreat Type</label>
+                                <select id="criteria-filter" class="form-control">
+                                    <option value="">All Types</option>
+                                    <option value="male_only">Men Only</option>
+                                    <option value="female_only">Women Only</option>
+                                    <option value="priests_only">Priests Only</option>
+                                    <option value="sisters_only">Sisters Only</option>
+                                    <option value="youth_only">Youth Only</option>
+                                    <option value="children">Children</option>
+                                    <option value="no_criteria">Open to All</option>
+                                </select>
+                            </div>
+                        </div>
+                        <div class="col-md-3">
+                            <div class="filter-group">
+                                <label for="search-filter">Search</label>
+                                <input type="text" id="search-filter" class="form-control" placeholder="Search retreats...">
+                            </div>
+                        </div>
+                        <div class="col-md-3">
+                            <div class="filter-group">
+                                <label>&nbsp;</label>
+                                <button id="clear-filters" class="btn btn-secondary btn-block">Clear Filters</button>
+                            </div>
+                        </div>
+                    </div>
+                </div>
+
+                <!-- Results Count -->
+                <div class="retreat-results-info">
+                    <p id="retreat-count">Loading...</p>
+                </div>
+
+                <!-- Retreat Cards Grid -->
+                <div id="retreat-grid" class="retreat-grid">
+                    <!-- Retreat cards will be loaded here via JavaScript -->
+                </div>
+
+                <!-- Pagination -->
+                <div id="retreat-pagination" class="pagination-container">
+                    <!-- Pagination will be added here if needed -->
+                </div>
+            </div>
+        </div>
+    </div>
+</div>
+
+<!-- Retreat Details Popup Modal -->
+<div id="retreat-details-modal" class="modal retreat-modal" tabindex="-1" role="dialog">
+    <div class="modal-dialog modal-lg" role="document">
+        <div class="modal-content">
+            <div class="modal-header">
+                <h5 class="modal-title" id="retreat-details-title">Retreat Details</h5>
+                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
+                    <span aria-hidden="true">&times;</span>
+                </button>
+            </div>
+            <div class="modal-body">
+                <div id="retreat-details-content">
+                    <!-- Retreat details will be loaded here -->
+                </div>
+            </div>
+            <div class="modal-footer">
+                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
+                <button type="button" class="btn btn-primary" id="book-retreat-btn">Book This Retreat</button>
+            </div>
+        </div>
+    </div>
+</div>
+
+<!-- Booking Form Popup Modal -->
+<div id="booking-form-modal" class="modal retreat-modal" tabindex="-1" role="dialog">
+    <div class="modal-dialog modal-xl" role="document">
+        <div class="modal-content">
+            <div class="modal-header">
+                <h5 class="modal-title">Book Your Retreat</h5>
+                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
+                    <span aria-hidden="true">&times;</span>
+                </button>
+            </div>
+            <div class="modal-body">
+                <div id="booking-form-content">
+                    <!-- Booking form will be loaded here -->
+                </div>
+            </div>
+        </div>
+    </div>
+</div>
+
+<!-- Booking Success Modal -->
+<div id="booking-success-modal" class="modal retreat-modal" tabindex="-1" role="dialog">
+    <div class="modal-dialog modal-lg" role="document">
+        <div class="modal-content">
+            <div class="modal-header bg-success text-white">
+                <h5 class="modal-title">Booking Successful!</h5>
+                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
+                    <span aria-hidden="true">&times;</span>
+                </button>
+            </div>
+            <div class="modal-body">
+                <div id="booking-success-content">
+                    <!-- Success content will be loaded here -->
+                </div>
+            </div>
+            <div class="modal-footer">
+                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
+            </div>
+        </div>
+    </div>
+</div>
+
+<!-- Manage Booking Modal -->
+<div id="manage-booking-modal" class="modal retreat-modal" tabindex="-1" role="dialog">
+    <div class="modal-dialog modal-lg" role="document">
+        <div class="modal-content">
+            <div class="modal-header">
+                <h5 class="modal-title">Manage Your Booking</h5>
+                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
+                    <span aria-hidden="true">&times;</span>
+                </button>
+            </div>
+            <div class="modal-body">
+                <!-- Booking verification form -->
+                <div id="booking-verification-form">
+                    <div class="row">
+                        <div class="col-md-6">
+                            <div class="form-group">
+                                <label for="verify-booking-id">Booking ID *</label>
+                                <input type="text" id="verify-booking-id" class="form-control" placeholder="e.g., RB123" required>
+                            </div>
+                        </div>
+                        <div class="col-md-6">
+                            <div class="form-group">
+                                <label for="verify-whatsapp">WhatsApp Number *</label>
+                                <input type="tel" id="verify-whatsapp" class="form-control" placeholder="Primary user's WhatsApp number" required>
+                            </div>
+                        </div>
+                    </div>
+                    <button type="button" id="verify-booking-btn" class="btn btn-primary">View Booking</button>
+                </div>
+
+                <!-- Booking details and management -->
+                <div id="booking-management-content" style="display: none;">
+                    <!-- Booking management content will be loaded here -->
+                </div>
+            </div>
+        </div>
+    </div>
+</div>
+
+<script type="text/template" id="retreat-card-template">
+<div class="retreat-card" data-retreat-id="{{retreat_id}}">
+    <div class="card">
+        {{#featured_image}}
+        <img class="card-img-top" src="{{featured_image}}" alt="{{retreat_name}}">
+        {{/featured_image}}
+        <div class="card-body">
+            <div class="retreat-header">
+                <h5 class="card-title">{{retreat_name}}</h5>
+                {{#is_featured}}
+                <span class="badge badge-featured">Featured</span>
+                {{/is_featured}}
+            </div>
+            
+            <div class="retreat-info">
+                <p class="retreat-dates">
+                    <i class="fas fa-calendar"></i>
+                    {{start_date}} to {{end_date}}
+                </p>
+                
+                <p class="retreat-location">
+                    <i class="fas fa-map-marker-alt"></i>
+                    {{location}}
+                </p>
+                
+                <div class="retreat-pricing">
+                    {{#discount_price}}
+                    <span class="price-original">₹{{price}}</span>
+                    <span class="price-discounted">₹{{discount_price}}</span>
+                    {{/discount_price}}
+                    {{^discount_price}}
+                    <span class="price">₹{{price}}</span>
+                    {{/discount_price}}
+                </div>
+                
+                <div class="retreat-availability">
+                    <span class="available-spots">{{available_spots}} spots available</span>
+                    <span class="criteria-badge">{{criteria_label}}</span>
+                </div>
+            </div>
+            
+            <div class="card-actions">
+                <button class="btn btn-outline-primary btn-sm view-details-btn" data-retreat-id="{{retreat_id}}">
+                    View Details
+                </button>
+                <button class="btn btn-primary btn-sm book-now-btn" data-retreat-id="{{retreat_id}}">
+                    Book Now
+                </button>
+            </div>
+        </div>
+    </div>
+</div>
+</script>
+
+<!-- Action Buttons (floating or fixed) -->
+<div class="retreat-actions-bar">
+    <button id="manage-booking-trigger" class="btn btn-outline-secondary">
+        <i class="fas fa-calendar-check"></i> Manage Booking
+    </button>
+</div>
+
+<?php
+// Enqueue necessary scripts and styles
+wp_enqueue_script('jquery');
+wp_enqueue_script('mustache-js', 'https://cdnjs.cloudflare.com/ajax/libs/mustache.js/4.2.0/mustache.min.js', array(), '4.2.0', true);
+
+// Pass WordPress AJAX URL and nonce to JavaScript
+wp_localize_script('jquery', 'retreat_ajax', array(
+    'ajax_url' => admin_url('admin-ajax.php'),
+    'nonce' => wp_create_nonce('retreat_ajax_nonce'),
+    'base_url' => home_url()
+));
+?>
+
+<style>
+.retreat-booking-page {
+    padding: 2rem 0;
+}
+
+.retreat-page-header {
+    text-align: center;
+    margin-bottom: 3rem;
+}
+
+.retreat-page-header h1 {
+    color: #2c3e50;
+    margin-bottom: 1rem;
+}
+
+.retreat-filters {
+    background: #f8f9fa;
+    padding: 2rem;
+    border-radius: 8px;
+    margin-bottom: 2rem;
+}
+
+.filter-group {
+    margin-bottom: 1rem;
+}
+
+.filter-group label {
+    font-weight: 600;
+    color: #495057;
+    margin-bottom: 0.5rem;
+}
+
+.retreat-results-info {
+    margin-bottom: 1.5rem;
+    padding: 0.5rem 0;
+    border-bottom: 2px solid #e9ecef;
+}
+
+.retreat-grid {
+    display: grid;
+    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
+    gap: 2rem;
+    margin-bottom: 3rem;
+}
+
+.retreat-card {
+    height: 100%;
+}
+
+.retreat-card .card {
+    height: 100%;
+    border: 1px solid #e0e0e0;
+    border-radius: 12px;
+    transition: all 0.3s ease;
+    overflow: hidden;
+}
+
+.retreat-card .card:hover {
+    transform: translateY(-5px);
+    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
+}
+
+.retreat-card .card-img-top {
+    height: 200px;
+    object-fit: cover;
+}
+
+.retreat-header {
+    display: flex;
+    justify-content: space-between;
+    align-items: flex-start;
+    margin-bottom: 1rem;
+}
+
+.retreat-header h5 {
+    color: #2c3e50;
+    font-weight: 600;
+    margin: 0;
+    flex: 1;
+}
+
+.badge-featured {
+    background-color: #f39c12;
+    color: white;
+    padding: 0.25rem 0.5rem;
+    border-radius: 4px;
+    font-size: 0.75rem;
+}
+
+.retreat-info p {
+    margin-bottom: 0.75rem;
+    color: #6c757d;
+    font-size: 0.9rem;
+}
+
+.retreat-info i {
+    width: 16px;
+    margin-right: 0.5rem;
+    color: #007bff;
+}
+
+.retreat-pricing {
+    margin: 1rem 0;
+}
+
+.price-original {
+    text-decoration: line-through;
+    color: #6c757d;
+    margin-right: 0.5rem;
+}
+
+.price-discounted, .price {
+    font-size: 1.25rem;
+    font-weight: 600;
+    color: #28a745;
+}
+
+.retreat-availability {
+    display: flex;
+    justify-content: space-between;
+    align-items: center;
+    margin: 1rem 0;
+}
+
+.available-spots {
+    color: #28a745;
+    font-weight: 500;
+    font-size: 0.85rem;
+}
+
+.criteria-badge {
+    background-color: #e9ecef;
+    padding: 0.25rem 0.5rem;
+    border-radius: 4px;
+    font-size: 0.75rem;
+    color: #495057;
+}
+
+.card-actions {
+    display: flex;
+    gap: 0.5rem;
+    padding-top: 1rem;
+    border-top: 1px solid #e9ecef;
+}
+
+.card-actions .btn {
+    flex: 1;
+}
+
+.loading-indicator {
+    text-align: center;
+    padding: 3rem;
+}
+
+.spinner {
+    border: 4px solid #f3f3f3;
+    border-top: 4px solid #007bff;
+    border-radius: 50%;
+    width: 40px;
+    height: 40px;
+    animation: spin 1s linear infinite;
+    margin: 0 auto 1rem;
+}
+
+@keyframes spin {
+    0% { transform: rotate(0deg); }
+    100% { transform: rotate(360deg); }
+}
+
+.retreat-actions-bar {
+    position: fixed;
+    bottom: 2rem;
+    right: 2rem;
+    z-index: 1000;
+}
+
+.retreat-modal .modal-dialog {
+    max-width: 90%;
+}
+
+@media (max-width: 768px) {
+    .retreat-grid {
+        grid-template-columns: 1fr;
+        gap: 1rem;
+    }
+    
+    .retreat-filters {
+        padding: 1rem;
+    }
+    
+    .retreat-filters .row {
+        margin: 0;
+    }
+    
+    .retreat-filters [class*="col-"] {
+        padding: 0.25rem;
+    }
+    
+    .retreat-actions-bar {
+        bottom: 1rem;
+        right: 1rem;
+    }
+}
+</style>
+
+<?php get_footer(); ?>

```



MarkAsCompleted: 1 items
Now let me create the comprehensive JavaScript file that will handle all the interactive features:


**Diff:**
```diff
--- E:\laragon\www\knk\wp-content\themes\your-theme\js\retreat-booking.js
+++ E:\laragon\www\knk\wp-content\themes\your-theme\js\retreat-booking.js
@@ -0,0 +1,1256 @@
+/**
+ * Retreat Booking JavaScript
+ * Handles all interactive features for the retreat booking system
+ */
+
+(function($) {
+    'use strict';
+
+    // Global variables
+    let retreatsData = [];
+    let filteredRetreats = [];
+    let currentRetreat = null;
+    let additionalParticipantsCount = 0;
+
+    // Initialize when document is ready
+    $(document).ready(function() {
+        initializeRetreatBooking();
+    });
+
+    function initializeRetreatBooking() {
+        // Load retreats on page load
+        loadRetreats();
+
+        // Setup event listeners
+        setupEventListeners();
+
+        // Initialize form validation
+        initializeFormValidation();
+    }
+
+    function setupEventListeners() {
+        // Filter events
+        $('#date-filter, #criteria-filter').on('change', applyFilters);
+        $('#search-filter').on('keyup', debounce(applyFilters, 300));
+        $('#clear-filters').on('click', clearFilters);
+
+        // Retreat card events
+        $(document).on('click', '.view-details-btn', handleViewDetails);
+        $(document).on('click', '.book-now-btn', handleBookNow);
+
+        // Modal events
+        $('#book-retreat-btn').on('click', showBookingForm);
+        $('#manage-booking-trigger').on('click', showManageBookingModal);
+        $('#verify-booking-btn').on('click', verifyAndShowBooking);
+
+        // Booking form events
+        $(document).on('change', '#additional-participants-count', handleAdditionalParticipantsChange);
+        $(document).on('click', '#submit-booking', handleBookingSubmission);
+        $(document).on('click', '.cancel-participant-btn', handleParticipantCancellation);
+
+        // Form validation events
+        $(document).on('blur', '.form-control[required]', validateField);
+        $(document).on('keyup', 'input[type="tel"]', formatPhoneNumber);
+    }
+
+    // Load retreats from API
+    function loadRetreats() {
+        showLoading(true);
+        
+        $.ajax({
+            url: retreat_ajax.ajax_url,
+            type: 'POST',
+            data: {
+                action: 'get_retreats',
+                nonce: retreat_ajax.nonce
+            },
+            success: function(response) {
+                if (response.success) {
+                    retreatsData = response.data.retreats;
+                    filteredRetreats = [...retreatsData];
+                    renderRetreats();
+                    updateResultsCount();
+                } else {
+                    showError('Failed to load retreats: ' + (response.data.message || 'Unknown error'));
+                }
+            },
+            error: function() {
+                showError('Failed to connect to the server. Please try again later.');
+            },
+            complete: function() {
+                showLoading(false);
+            }
+        });
+    }
+
+    // Render retreat cards
+    function renderRetreats() {
+        const template = $('#retreat-card-template').html();
+        const $grid = $('#retreat-grid');
+        
+        if (filteredRetreats.length === 0) {
+            $grid.html('<div class="no-results"><h4>No retreats found</h4><p>Try adjusting your filters or check back later for new retreats.</p></div>');
+            return;
+        }
+
+        let html = '';
+        filteredRetreats.forEach(function(retreat) {
+            // Format dates
+            retreat.start_date = formatDate(retreat.start_date);
+            retreat.end_date = formatDate(retreat.end_date);
+            
+            // Render card using Mustache template
+            html += Mustache.render(template, retreat);
+        });
+
+        $grid.html(html);
+    }
+
+    // Apply filters
+    function applyFilters() {
+        const dateFilter = $('#date-filter').val();
+        const criteriaFilter = $('#criteria-filter').val();
+        const searchFilter = $('#search-filter').val().toLowerCase().trim();
+
+        filteredRetreats = retreatsData.filter(function(retreat) {
+            // Date filter
+            if (dateFilter) {
+                const startDate = new Date(retreat.start_date);
+                const now = new Date();
+                const thisMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
+                const nextMonth = new Date(now.getFullYear(), now.getMonth() + 2, 0);
+                const next3Months = new Date(now.getFullYear(), now.getMonth() + 4, 0);
+
+                switch (dateFilter) {
+                    case 'this-month':
+                        if (startDate > thisMonth) return false;
+                        break;
+                    case 'next-month':
+                        if (startDate > nextMonth) return false;
+                        break;
+                    case 'next-3-months':
+                        if (startDate > next3Months) return false;
+                        break;
+                }
+            }
+
+            // Criteria filter
+            if (criteriaFilter && retreat.criteria !== criteriaFilter) {
+                return false;
+            }
+
+            // Search filter
+            if (searchFilter) {
+                const searchableText = (
+                    retreat.retreat_name + ' ' +
+                    retreat.location + ' ' +
+                    (retreat.criteria_label || '')
+                ).toLowerCase();
+                
+                if (!searchableText.includes(searchFilter)) {
+                    return false;
+                }
+            }
+
+            return true;
+        });
+
+        renderRetreats();
+        updateResultsCount();
+    }
+
+    // Clear all filters
+    function clearFilters() {
+        $('#date-filter, #criteria-filter').val('');
+        $('#search-filter').val('');
+        applyFilters();
+    }
+
+    // Handle view details button click
+    function handleViewDetails(e) {
+        const retreatId = $(this).data('retreat-id');
+        loadRetreatDetails(retreatId);
+    }
+
+    // Handle book now button click
+    function handleBookNow(e) {
+        const retreatId = $(this).data('retreat-id');
+        currentRetreat = retreatsData.find(r => r.retreat_id === retreatId);
+        if (currentRetreat) {
+            showBookingForm();
+        }
+    }
+
+    // Load retreat details
+    function loadRetreatDetails(retreatId) {
+        $.ajax({
+            url: retreat_ajax.ajax_url,
+            type: 'POST',
+            data: {
+                action: 'get_retreat_details',
+                retreat_id: retreatId,
+                nonce: retreat_ajax.nonce
+            },
+            success: function(response) {
+                if (response.success) {
+                    showRetreatDetailsModal(response.data.retreat);
+                    currentRetreat = response.data.retreat;
+                } else {
+                    showError('Failed to load retreat details: ' + response.data.message);
+                }
+            },
+            error: function() {
+                showError('Failed to load retreat details. Please try again.');
+            }
+        });
+    }
+
+    // Show retreat details modal
+    function showRetreatDetailsModal(retreat) {
+        const $modal = $('#retreat-details-modal');
+        const $content = $('#retreat-details-content');
+
+        $('#retreat-details-title').text(retreat.retreat_name);
+        
+        let detailsHtml = `
+            <div class="retreat-detail-view">
+                ${retreat.featured_image ? `<img src="${retreat.featured_image}" class="img-fluid mb-3" alt="${retreat.retreat_name}">` : ''}
+                
+                <div class="retreat-basic-info">
+                    <div class="row">
+                        <div class="col-md-6">
+                            <h6><i class="fas fa-calendar"></i> Dates</h6>
+                            <p>${formatDate(retreat.start_date)} to ${formatDate(retreat.end_date)}</p>
+                        </div>
+                        <div class="col-md-6">
+                            <h6><i class="fas fa-clock"></i> Timings</h6>
+                            <p>${retreat.timings || 'Not specified'}</p>
+                        </div>
+                    </div>
+                    
+                    <div class="row">
+                        <div class="col-md-6">
+                            <h6><i class="fas fa-map-marker-alt"></i> Location</h6>
+                            <p>${retreat.location?.name || retreat.location}</p>
+                            ${retreat.location?.address ? `<small class="text-muted">${retreat.location.address}</small>` : ''}
+                        </div>
+                        <div class="col-md-6">
+                            <h6><i class="fas fa-users"></i> Availability</h6>
+                            <p class="text-success">${retreat.availability?.available_spots || 0} spots available</p>
+                        </div>
+                    </div>
+                </div>
+
+                <div class="retreat-pricing-detail mt-3">
+                    <h6>Pricing</h6>
+                    <div class="pricing-info">
+                        ${retreat.pricing?.has_discount ? 
+                            `<span class="price-original">₹${retreat.pricing.price}</span>
+                             <span class="price-discounted">₹${retreat.pricing.effective_price}</span>
+                             <span class="discount-badge">${retreat.pricing.discount_percentage}% OFF</span>` :
+                            `<span class="price">₹${retreat.pricing?.effective_price || retreat.price}</span>`
+                        }
+                    </div>
+                </div>
+
+                <div class="retreat-description mt-3">
+                    <h6>Description</h6>
+                    <p>${retreat.description || 'No description available'}</p>
+                </div>
+
+                ${retreat.criteria?.label ? `
+                    <div class="retreat-criteria mt-3">
+                        <h6>Eligibility</h6>
+                        <span class="badge badge-info">${retreat.criteria.label}</span>
+                    </div>
+                ` : ''}
+
+                ${retreat.details?.instructions ? `
+                    <div class="retreat-instructions mt-3">
+                        <h6>Instructions</h6>
+                        <p>${retreat.details.instructions}</p>
+                    </div>
+                ` : ''}
+            </div>
+        `;
+
+        $content.html(detailsHtml);
+        $modal.modal('show');
+    }
+
+    // Show booking form
+    function showBookingForm() {
+        if (!currentRetreat) {
+            showError('Please select a retreat first');
+            return;
+        }
+
+        const $modal = $('#booking-form-modal');
+        const $content = $('#booking-form-content');
+
+        let formHtml = generateBookingFormHTML();
+        $content.html(formHtml);
+
+        // Close details modal if open
+        $('#retreat-details-modal').modal('hide');
+        
+        // Show booking modal
+        $modal.modal('show');
+
+        // Initialize form
+        additionalParticipantsCount = 0;
+        updateAdditionalParticipantsForm();
+    }
+
+    // Generate booking form HTML
+    function generateBookingFormHTML() {
+        return `
+            <div class="booking-form-container">
+                <div class="retreat-summary-header">
+                    <h6>Booking for: ${currentRetreat.retreat_name || currentRetreat.title}</h6>
+                    <p class="text-muted">${formatDate(currentRetreat.start_date)} to ${formatDate(currentRetreat.end_date)}</p>
+                </div>
+
+                <form id="retreat-booking-form" novalidate>
+                    <div class="form-section">
+                        <h5>Primary Participant Details</h5>
+                        <div class="row">
+                            <div class="col-md-6">
+                                <div class="form-group">
+                                    <label for="firstname">First Name *</label>
+                                    <input type="text" class="form-control" id="firstname" name="firstname" required>
+                                    <div class="invalid-feedback"></div>
+                                </div>
+                            </div>
+                            <div class="col-md-6">
+                                <div class="form-group">
+                                    <label for="lastname">Last Name *</label>
+                                    <input type="text" class="form-control" id="lastname" name="lastname" required>
+                                    <div class="invalid-feedback"></div>
+                                </div>
+                            </div>
+                        </div>
+                        
+                        <div class="row">
+                            <div class="col-md-6">
+                                <div class="form-group">
+                                    <label for="whatsapp_number">WhatsApp Number *</label>
+                                    <input type="tel" class="form-control" id="whatsapp_number" name="whatsapp_number" 
+                                           placeholder="10-digit number" maxlength="10" required>
+                                    <div class="invalid-feedback"></div>
+                                </div>
+                            </div>
+                            <div class="col-md-3">
+                                <div class="form-group">
+                                    <label for="age">Age *</label>
+                                    <input type="number" class="form-control" id="age" name="age" min="1" max="120" required>
+                                    <div class="invalid-feedback"></div>
+                                </div>
+                            </div>
+                            <div class="col-md-3">
+                                <div class="form-group">
+                                    <label for="gender">Gender *</label>
+                                    <select class="form-control" id="gender" name="gender" required>
+                                        <option value="">Select Gender</option>
+                                        <option value="male">Male</option>
+                                        <option value="female">Female</option>
+                                        <option value="other">Other</option>
+                                    </select>
+                                    <div class="invalid-feedback"></div>
+                                </div>
+                            </div>
+                        </div>
+
+                        <div class="form-group">
+                            <label for="email">Email Address *</label>
+                            <input type="email" class="form-control" id="email" name="email" required>
+                            <div class="invalid-feedback"></div>
+                        </div>
+
+                        <div class="form-group">
+                            <label for="address">Address *</label>
+                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
+                            <div class="invalid-feedback"></div>
+                        </div>
+
+                        <div class="row">
+                            <div class="col-md-6">
+                                <div class="form-group">
+                                    <label for="city">City *</label>
+                                    <input type="text" class="form-control" id="city" name="city" required>
+                                    <div class="invalid-feedback"></div>
+                                </div>
+                            </div>
+                            <div class="col-md-6">
+                                <div class="form-group">
+                                    <label for="state">State *</label>
+                                    <input type="text" class="form-control" id="state" name="state" required>
+                                    <div class="invalid-feedback"></div>
+                                </div>
+                            </div>
+                        </div>
+
+                        <div class="row">
+                            <div class="col-md-4">
+                                <div class="form-group">
+                                    <label for="diocese">Diocese</label>
+                                    <input type="text" class="form-control" id="diocese" name="diocese">
+                                </div>
+                            </div>
+                            <div class="col-md-4">
+                                <div class="form-group">
+                                    <label for="parish">Parish</label>
+                                    <input type="text" class="form-control" id="parish" name="parish">
+                                </div>
+                            </div>
+                            <div class="col-md-4">
+                                <div class="form-group">
+                                    <label for="congregation">Congregation ${isRetreatRequiresCongregation() ? '*' : ''}</label>
+                                    <input type="text" class="form-control" id="congregation" name="congregation" 
+                                           ${isRetreatRequiresCongregation() ? 'required' : ''}>
+                                    <div class="invalid-feedback"></div>
+                                </div>
+                            </div>
+                        </div>
+
+                        <div class="row">
+                            <div class="col-md-6">
+                                <div class="form-group">
+                                    <label for="emergency_contact_name">Emergency Contact Name *</label>
+                                    <input type="text" class="form-control" id="emergency_contact_name" 
+                                           name="emergency_contact_name" required>
+                                    <div class="invalid-feedback"></div>
+                                </div>
+                            </div>
+                            <div class="col-md-6">
+                                <div class="form-group">
+                                    <label for="emergency_contact_phone">Emergency Contact Phone *</label>
+                                    <input type="tel" class="form-control" id="emergency_contact_phone" 
+                                           name="emergency_contact_phone" required>
+                                    <div class="invalid-feedback"></div>
+                                </div>
+                            </div>
+                        </div>
+                    </div>
+
+                    <div class="form-section">
+                        <h5>Additional Participants</h5>
+                        <div class="form-group">
+                            <label for="additional-participants-count">Number of Additional Participants (Maximum 3)</label>
+                            <select class="form-control" id="additional-participants-count">
+                                <option value="0">0 - Just me</option>
+                                <option value="1">1 additional participant</option>
+                                <option value="2">2 additional participants</option>
+                                <option value="3">3 additional participants</option>
+                            </select>
+                        </div>
+                        
+                        <div id="additional-participants-forms">
+                            <!-- Additional participant forms will be inserted here -->
+                        </div>
+                    </div>
+
+                    <div class="form-section">
+                        <div class="form-group">
+                            <label for="special_remarks">Special Remarks</label>
+                            <textarea class="form-control" id="special_remarks" name="special_remarks" 
+                                      rows="3" placeholder="Any special requirements or remarks..."></textarea>
+                        </div>
+                    </div>
+
+                    <div class="booking-summary">
+                        <h6>Booking Summary</h6>
+                        <div class="summary-row">
+                            <span>Retreat:</span>
+                            <span>${currentRetreat.retreat_name || currentRetreat.title}</span>
+                        </div>
+                        <div class="summary-row">
+                            <span>Price per person:</span>
+                            <span>₹${currentRetreat.pricing?.effective_price || currentRetreat.price}</span>
+                        </div>
+                        <div class="summary-row" id="total-participants-summary">
+                            <span>Total participants:</span>
+                            <span>1</span>
+                        </div>
+                        <div class="summary-row total-row" id="total-cost-summary">
+                            <strong>
+                                <span>Total Cost:</span>
+                                <span>₹${currentRetreat.pricing?.effective_price || currentRetreat.price}</span>
+                            </strong>
+                        </div>
+                    </div>
+
+                    <div class="form-actions">
+                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
+                        <button type="submit" id="submit-booking" class="btn btn-primary">
+                            <span class="btn-text">Submit Booking</span>
+                            <span class="btn-loading" style="display: none;">
+                                <i class="fas fa-spinner fa-spin"></i> Processing...
+                            </span>
+                        </button>
+                    </div>
+                </form>
+            </div>
+        `;
+    }
+
+    // Check if retreat requires congregation field
+    function isRetreatRequiresCongregation() {
+        return currentRetreat && ['priests_only', 'sisters_only'].includes(currentRetreat.criteria?.type);
+    }
+
+    // Handle additional participants count change
+    function handleAdditionalParticipantsChange() {
+        additionalParticipantsCount = parseInt($(this).val()) || 0;
+        updateAdditionalParticipantsForm();
+        updateBookingSummary();
+    }
+
+    // Update additional participants form
+    function updateAdditionalParticipantsForm() {
+        const $container = $('#additional-participants-forms');
+        let html = '';
+
+        for (let i = 1; i <= additionalParticipantsCount; i++) {
+            html += generateAdditionalParticipantForm(i);
+        }
+
+        $container.html(html);
+    }
+
+    // Generate additional participant form HTML
+    function generateAdditionalParticipantForm(index) {
+        return `
+            <div class="additional-participant-form" data-participant="${index}">
+                <h6>Additional Participant ${index}</h6>
+                <div class="row">
+                    <div class="col-md-6">
+                        <div class="form-group">
+                            <label for="participant_${index}_firstname">First Name *</label>
+                            <input type="text" class="form-control" 
+                                   id="participant_${index}_firstname" 
+                                   name="participants[${index-1}][firstname]" required>
+                            <div class="invalid-feedback"></div>
+                        </div>
+                    </div>
+                    <div class="col-md-6">
+                        <div class="form-group">
+                            <label for="participant_${index}_lastname">Last Name *</label>
+                            <input type="text" class="form-control" 
+                                   id="participant_${index}_lastname" 
+                                   name="participants[${index-1}][lastname]" required>
+                            <div class="invalid-feedback"></div>
+                        </div>
+                    </div>
+                </div>
+                <div class="row">
+                    <div class="col-md-4">
+                        <div class="form-group">
+                            <label for="participant_${index}_whatsapp_number">WhatsApp Number *</label>
+                            <input type="tel" class="form-control" 
+                                   id="participant_${index}_whatsapp_number" 
+                                   name="participants[${index-1}][whatsapp_number]" 
+                                   maxlength="10" required>
+                            <div class="invalid-feedback"></div>
+                        </div>
+                    </div>
+                    <div class="col-md-4">
+                        <div class="form-group">
+                            <label for="participant_${index}_age">Age *</label>
+                            <input type="number" class="form-control" 
+                                   id="participant_${index}_age" 
+                                   name="participants[${index-1}][age]" 
+                                   min="1" max="120" required>
+                            <div class="invalid-feedback"></div>
+                        </div>
+                    </div>
+                    <div class="col-md-4">
+                        <div class="form-group">
+                            <label for="participant_${index}_gender">Gender *</label>
+                            <select class="form-control" 
+                                    id="participant_${index}_gender" 
+                                    name="participants[${index-1}][gender]" required>
+                                <option value="">Select Gender</option>
+                                <option value="male">Male</option>
+                                <option value="female">Female</option>
+                                <option value="other">Other</option>
+                            </select>
+                            <div class="invalid-feedback"></div>
+                        </div>
+                    </div>
+                </div>
+                <div class="form-group">
+                    <label for="participant_${index}_email">Email Address *</label>
+                    <input type="email" class="form-control" 
+                           id="participant_${index}_email" 
+                           name="participants[${index-1}][email]" required>
+                    <div class="invalid-feedback"></div>
+                </div>
+            </div>
+        `;
+    }
+
+    // Update booking summary
+    function updateBookingSummary() {
+        const totalParticipants = 1 + additionalParticipantsCount;
+        const pricePerPerson = currentRetreat.pricing?.effective_price || currentRetreat.price || 0;
+        const totalCost = totalParticipants * pricePerPerson;
+
+        $('#total-participants-summary span:last').text(totalParticipants);
+        $('#total-cost-summary span:last').text('₹' + totalCost);
+    }
+
+    // Handle booking form submission
+    function handleBookingSubmission(e) {
+        e.preventDefault();
+        
+        if (!validateBookingForm()) {
+            return;
+        }
+
+        const $submitBtn = $('#submit-booking');
+        $submitBtn.prop('disabled', true).find('.btn-text').hide().end().find('.btn-loading').show();
+
+        const formData = collectBookingFormData();
+
+        $.ajax({
+            url: retreat_ajax.ajax_url,
+            type: 'POST',
+            data: {
+                action: 'submit_booking',
+                booking_data: formData,
+                nonce: retreat_ajax.nonce
+            },
+            success: function(response) {
+                if (response.success) {
+                    $('#booking-form-modal').modal('hide');
+                    showBookingSuccess(response.data.booking);
+                } else {
+                    showBookingError(response.data.message, response.data.errors);
+                }
+            },
+            error: function() {
+                showError('Failed to submit booking. Please try again.');
+            },
+            complete: function() {
+                $submitBtn.prop('disabled', false).find('.btn-text').show().end().find('.btn-loading').hide();
+            }
+        });
+    }
+
+    // Collect booking form data
+    function collectBookingFormData() {
+        const $form = $('#retreat-booking-form');
+        const participants = [];
+
+        // Primary participant
+        const primaryParticipant = {
+            firstname: $('#firstname').val(),
+            lastname: $('#lastname').val(),
+            whatsapp_number: $('#whatsapp_number').val(),
+            age: parseInt($('#age').val()),
+            email: $('#email').val(),
+            address: $('#address').val(),
+            city: $('#city').val(),
+            state: $('#state').val(),
+            gender: $('#gender').val(),
+            diocese: $('#diocese').val() || null,
+            parish: $('#parish').val() || null,
+            congregation: $('#congregation').val() || null,
+            emergency_contact_name: $('#emergency_contact_name').val(),
+            emergency_contact_phone: $('#emergency_contact_phone').val(),
+            special_remarks: $('#special_remarks').val() || null
+        };
+
+        participants.push(primaryParticipant);
+
+        // Additional participants
+        for (let i = 1; i <= additionalParticipantsCount; i++) {
+            const participant = {
+                firstname: $(`#participant_${i}_firstname`).val(),
+                lastname: $(`#participant_${i}_lastname`).val(),
+                whatsapp_number: $(`#participant_${i}_whatsapp_number`).val(),
+                age: parseInt($(`#participant_${i}_age`).val()),
+                email: $(`#participant_${i}_email`).val(),
+                gender: $(`#participant_${i}_gender`).val(),
+                address: primaryParticipant.address, // Use primary's address
+                city: primaryParticipant.city,
+                state: primaryParticipant.state,
+                diocese: primaryParticipant.diocese,
+                parish: primaryParticipant.parish,
+                congregation: primaryParticipant.congregation,
+                emergency_contact_name: primaryParticipant.emergency_contact_name,
+                emergency_contact_phone: primaryParticipant.emergency_contact_phone
+            };
+            participants.push(participant);
+        }
+
+        return {
+            retreat_id: currentRetreat.retreat_id,
+            participants: participants
+        };
+    }
+
+    // Validate booking form
+    function validateBookingForm() {
+        let isValid = true;
+        const $form = $('#retreat-booking-form');
+
+        // Clear previous validation
+        $form.find('.is-invalid').removeClass('is-invalid');
+        $form.find('.invalid-feedback').text('');
+
+        // Validate primary participant
+        isValid = validateParticipant('', isRetreatRequiresCongregation()) && isValid;
+
+        // Validate additional participants
+        for (let i = 1; i <= additionalParticipantsCount; i++) {
+            isValid = validateParticipant(`participant_${i}_`, false) && isValid;
+        }
+
+        return isValid;
+    }
+
+    // Validate individual participant
+    function validateParticipant(prefix, requireCongregation = false) {
+        let isValid = true;
+        const fields = [
+            { id: `${prefix}firstname`, required: true, message: 'First name is required' },
+            { id: `${prefix}lastname`, required: true, message: 'Last name is required' },
+            { id: `${prefix}whatsapp_number`, required: true, message: 'WhatsApp number is required', validation: 'phone' },
+            { id: `${prefix}age`, required: true, message: 'Age is required', validation: 'age' },
+            { id: `${prefix}email`, required: true, message: 'Email address is required', validation: 'email' },
+            { id: `${prefix}gender`, required: true, message: 'Gender is required' }
+        ];
+
+        // Add fields specific to primary participant
+        if (!prefix) {
+            fields.push(
+                { id: 'address', required: true, message: 'Address is required' },
+                { id: 'city', required: true, message: 'City is required' },
+                { id: 'state', required: true, message: 'State is required' },
+                { id: 'emergency_contact_name', required: true, message: 'Emergency contact name is required' },
+                { id: 'emergency_contact_phone', required: true, message: 'Emergency contact phone is required' }
+            );
+
+            if (requireCongregation) {
+                fields.push({ id: 'congregation', required: true, message: 'Congregation is required for this retreat' });
+            }
+        }
+
+        fields.forEach(function(field) {
+            const $field = $(`#${field.id}`);
+            const value = $field.val().trim();
+
+            if (field.required && !value) {
+                markFieldInvalid($field, field.message);
+                isValid = false;
+            } else if (value && field.validation) {
+                let validationResult = true;
+                let validationMessage = '';
+
+                switch (field.validation) {
+                    case 'phone':
+                        validationResult = /^\d{10}$/.test(value);
+                        validationMessage = 'WhatsApp number must be exactly 10 digits';
+                        break;
+                    case 'email':
+                        validationResult = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
+                        validationMessage = 'Please enter a valid email address';
+                        break;
+                    case 'age':
+                        const age = parseInt(value);
+                        validationResult = age >= 1 && age <= 120;
+                        validationMessage = 'Age must be between 1 and 120';
+                        break;
+                }
+
+                if (!validationResult) {
+                    markFieldInvalid($field, validationMessage);
+                    isValid = false;
+                }
+            }
+        });
+
+        return isValid;
+    }
+
+    // Mark field as invalid
+    function markFieldInvalid($field, message) {
+        $field.addClass('is-invalid');
+        $field.next('.invalid-feedback').text(message);
+    }
+
+    // Validate individual field on blur
+    function validateField() {
+        const $field = $(this);
+        $field.removeClass('is-invalid');
+        $field.next('.invalid-feedback').text('');
+
+        // Add specific validation logic here if needed
+    }
+
+    // Format phone number input
+    function formatPhoneNumber() {
+        let value = $(this).val().replace(/\D/g, '');
+        if (value.length > 10) {
+            value = value.substring(0, 10);
+        }
+        $(this).val(value);
+    }
+
+    // Show booking success modal
+    function showBookingSuccess(bookingData) {
+        const $modal = $('#booking-success-modal');
+        const $content = $('#booking-success-content');
+
+        // Generate WhatsApp community URL and QR code
+        const whatsappMessage = `Hi! I've successfully booked a retreat (Booking ID: ${bookingData.booking_id}). Looking forward to joining the community!`;
+        const whatsappUrl = generateWhatsAppCommunityUrl(whatsappMessage);
+        const qrCodeUrl = `https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=${encodeURIComponent(whatsappUrl)}`;
+
+        const successHtml = `
+            <div class="booking-success-content">
+                <div class="text-center mb-4">
+                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
+                    <h4 class="mt-2">Booking Confirmed!</h4>
+                </div>
+
+                <div class="booking-details">
+                    <div class="alert alert-success">
+                        <h5>Your Booking ID: <strong>${bookingData.booking_id}</strong></h5>
+                        <p class="mb-0">Please save this booking ID for future reference.</p>
+                    </div>
+
+                    <div class="retreat-booking-info">
+                        <h6>Retreat Details:</h6>
+                        <ul class="list-unstyled">
+                            <li><strong>Retreat:</strong> ${bookingData.retreat.name}</li>
+                            <li><strong>Dates:</strong> ${bookingData.retreat.start_date} to ${bookingData.retreat.end_date}</li>
+                            <li><strong>Participants:</strong> ${bookingData.total_participants}</li>
+                        </ul>
+                    </div>
+
+                    <div class="whatsapp-community mt-4">
+                        <h6>Join Our WhatsApp Community</h6>
+                        <p>Connect with other retreat participants and stay updated:</p>
+                        
+                        <div class="row text-center">
+                            <div class="col-md-6">
+                                <div class="qr-code-section">
+                                    <img src="${qrCodeUrl}" alt="WhatsApp QR Code" class="img-fluid">
+                                    <p class="small text-muted">Scan with your phone</p>
+                                </div>
+                            </div>
+                            <div class="col-md-6">
+                                <div class="direct-link-section">
+                                    <a href="${whatsappUrl}" class="btn btn-success btn-lg" target="_blank">
+                                        <i class="fab fa-whatsapp"></i> Join Community
+                                    </a>
+                                    <p class="small text-muted mt-2">Click to join directly</p>
+                                </div>
+                            </div>
+                        </div>
+                    </div>
+
+                    <div class="next-steps mt-4">
+                        <h6>What's Next?</h6>
+                        <ul>
+                            <li>Check your email for confirmation details</li>
+                            <li>Join our WhatsApp community for updates</li>
+                            <li>Prepare for your spiritual journey</li>
+                        </ul>
+                    </div>
+                </div>
+            </div>
+        `;
+
+        $content.html(successHtml);
+        $modal.modal('show');
+    }
+
+    // Show manage booking modal
+    function showManageBookingModal() {
+        $('#manage-booking-modal').modal('show');
+        $('#booking-verification-form').show();
+        $('#booking-management-content').hide();
+    }
+
+    // Verify and show booking
+    function verifyAndShowBooking() {
+        const bookingId = $('#verify-booking-id').val().trim();
+        const whatsappNumber = $('#verify-whatsapp').val().trim();
+
+        if (!bookingId || !whatsappNumber) {
+            showError('Please enter both Booking ID and WhatsApp number');
+            return;
+        }
+
+        if (!isValidPhoneNumber(whatsappNumber)) {
+            showError('Please enter a valid 10-digit WhatsApp number');
+            return;
+        }
+
+        $.ajax({
+            url: retreat_ajax.ajax_url,
+            type: 'POST',
+            data: {
+                action: 'get_booking',
+                booking_id: bookingId,
+                whatsapp_number: whatsappNumber,
+                nonce: retreat_ajax.nonce
+            },
+            success: function(response) {
+                if (response.success) {
+                    showBookingManagement(response.data.booking);
+                } else {
+                    showError(response.data.message);
+                }
+            },
+            error: function() {
+                showError('Failed to retrieve booking. Please try again.');
+            }
+        });
+    }
+
+    // Show booking management interface
+    function showBookingManagement(bookingData) {
+        const $container = $('#booking-management-content');
+        
+        let managementHtml = `
+            <div class="booking-management">
+                <div class="booking-header">
+                    <h5>Booking Details: ${bookingData.booking_id}</h5>
+                    <span class="badge badge-${getStatusBadgeClass(bookingData.status)}">${bookingData.status}</span>
+                </div>
+
+                <div class="retreat-info-summary">
+                    <h6>${bookingData.retreat.name}</h6>
+                    <p class="text-muted">${bookingData.retreat.start_date} to ${bookingData.retreat.end_date}</p>
+                </div>
+
+                <div class="participants-list">
+                    <h6>Participants (${bookingData.participants.length})</h6>
+                    <div class="table-responsive">
+                        <table class="table table-sm">
+                            <thead>
+                                <tr>
+                                    <th><input type="checkbox" id="select-all-participants"></th>
+                                    <th>Name</th>
+                                    <th>Role</th>
+                                    <th>WhatsApp</th>
+                                    <th>Email</th>
+                                    <th>Action</th>
+                                </tr>
+                            </thead>
+                            <tbody>
+        `;
+
+        bookingData.participants.forEach(function(participant) {
+            const isPrimary = participant.role === 'primary';
+            managementHtml += `
+                <tr>
+                    <td>
+                        <input type="checkbox" class="participant-checkbox" 
+                               data-serial="${participant.serial_number}" 
+                               ${isPrimary ? 'data-is-primary="true"' : ''}>
+                    </td>
+                    <td>${participant.full_name}</td>
+                    <td>
+                        ${isPrimary ? '<span class="badge badge-primary">Primary</span>' : 'Additional'}
+                    </td>
+                    <td>${participant.whatsapp_number}</td>
+                    <td>${participant.email}</td>
+                    <td>
+                        <button class="btn btn-sm btn-outline-danger cancel-participant-btn" 
+                                data-serial="${participant.serial_number}"
+                                ${bookingData.important_info?.is_cancellable ? '' : 'disabled'}>
+                            Cancel
+                        </button>
+                    </td>
+                </tr>
+            `;
+        });
+
+        managementHtml += `
+                            </tbody>
+                        </table>
+                    </div>
+                </div>
+
+                ${bookingData.important_info?.is_cancellable ? `
+                    <div class="bulk-actions mt-3">
+                        <button id="cancel-selected-btn" class="btn btn-warning" disabled>
+                            Cancel Selected Participants
+                        </button>
+                        <small class="text-muted d-block mt-1">
+                            Note: Cancelling the primary participant will cancel all participants
+                        </small>
+                    </div>
+                ` : `
+                    <div class="alert alert-info mt-3">
+                        <i class="fas fa-info-circle"></i>
+                        Cancellation is not allowed as the retreat is starting soon or has already started.
+                    </div>
+                `}
+
+                <div class="booking-summary mt-4">
+                    <h6>Additional Information</h6>
+                    <ul class="list-unstyled">
+                        <li><strong>Booking Date:</strong> ${bookingData.booking_date}</li>
+                        <li><strong>Retreat Status:</strong> ${bookingData.retreat_status}</li>
+                        ${bookingData.important_info?.days_until_retreat !== null ? 
+                            `<li><strong>Days Until Retreat:</strong> ${bookingData.important_info.days_until_retreat}</li>` : ''}
+                    </ul>
+                </div>
+            </div>
+        `;
+
+        $container.html(managementHtml);
+        $('#booking-verification-form').hide();
+        $container.show();
+
+        // Setup participant selection logic
+        setupParticipantSelection();
+    }
+
+    // Setup participant selection logic
+    function setupParticipantSelection() {
+        const $selectAll = $('#select-all-participants');
+        const $checkboxes = $('.participant-checkbox');
+        const $cancelSelectedBtn = $('#cancel-selected-btn');
+
+        // Select all functionality
+        $selectAll.on('change', function() {
+            const isChecked = $(this).is(':checked');
+            $checkboxes.prop('checked', isChecked);
+            updateCancelSelectedButton();
+        });
+
+        // Individual checkbox change
+        $checkboxes.on('change', function() {
+            const isPrimary = $(this).data('is-primary');
+            
+            if (isPrimary && $(this).is(':checked')) {
+                // If primary is selected, select all others
+                $checkboxes.prop('checked', true);
+                $selectAll.prop('checked', true);
+            }
+            
+            updateCancelSelectedButton();
+        });
+
+        // Cancel selected button
+        $cancelSelectedBtn.on('click', handleBulkCancellation);
+
+        function updateCancelSelectedButton() {
+            const selectedCount = $checkboxes.filter(':checked').length;
+            $cancelSelectedBtn.prop('disabled', selectedCount === 0);
+            
+            if (selectedCount > 0) {
+                $cancelSelectedBtn.text(`Cancel Selected (${selectedCount})`);
+            } else {
+                $cancelSelectedBtn.text('Cancel Selected Participants');
+            }
+        }
+    }
+
+    // Handle individual participant cancellation
+    function handleParticipantCancellation() {
+        const serialNumber = $(this).data('serial');
+        const bookingId = $('#verify-booking-id').val();
+        
+        if (confirm('Are you sure you want to cancel this participant?')) {
+            cancelParticipant(bookingId, serialNumber);
+        }
+    }
+
+    // Handle bulk cancellation
+    function handleBulkCancellation() {
+        const $selectedCheckboxes = $('.participant-checkbox:checked');
+        
+        if ($selectedCheckboxes.length === 0) {
+            return;
+        }
+
+        const selectedSerials = [];
+        $selectedCheckboxes.each(function() {
+            selectedSerials.push($(this).data('serial'));
+        });
+
+        const confirmMessage = `Are you sure you want to cancel ${selectedSerials.length} participant(s)?`;
+        
+        if (confirm(confirmMessage)) {
+            // Cancel participants one by one (the API handles one at a time)
+            const bookingId = $('#verify-booking-id').val();
+            
+            // Start with the first serial number
+            cancelMultipleParticipants(bookingId, selectedSerials, 0);
+        }
+    }
+
+    // Cancel multiple participants recursively
+    function cancelMultipleParticipants(bookingId, serialNumbers, index) {
+        if (index >= serialNumbers.length) {
+            // All cancellations completed, refresh the booking view
+            verifyAndShowBooking();
+            return;
+        }
+
+        cancelParticipant(bookingId, serialNumbers[index], function() {
+            // Continue with next participant
+            cancelMultipleParticipants(bookingId, serialNumbers, index + 1);
+        });
+    }
+
+    // Cancel individual participant
+    function cancelParticipant(bookingId, serialNumber, callback) {
+        $.ajax({
+            url: retreat_ajax.ajax_url,
+            type: 'POST',
+            data: {
+                action: 'cancel_booking_participant',
+                booking_id: bookingId,
+                serial_number: serialNumber,
+                nonce: retreat_ajax.nonce
+            },
+            success: function(response) {
+                if (response.success) {
+                    showSuccess('Participant cancelled successfully');
+                    if (callback) callback();
+                } else {
+                    showError('Failed to cancel participant: ' + response.data.message);
+                }
+            },
+            error: function() {
+                showError('Failed to cancel participant. Please try again.');
+            }
+        });
+    }
+
+    // Utility functions
+    function showLoading(show) {
+        const $loading = $('#retreat-loading');
+        if (show) {
+            $loading.show();
+            $('#retreat-grid').hide();
+        } else {
+            $loading.hide();
+            $('#retreat-grid').show();
+        }
+    }
+
+    function showError(message) {
+        const $errorDiv = $('#retreat-error-message');
+        $errorDiv.html('<i class="fas fa-exclamation-triangle"></i> ' + message).show();
+        
+        // Hide after 5 seconds
+        setTimeout(function() {
+            $errorDiv.fadeOut();
+        }, 5000);
+    }
+
+    function showSuccess(message) {
+        const $errorDiv = $('#retreat-error-message');
+        $errorDiv.removeClass('alert-danger').addClass('alert-success');
+        $errorDiv.html('<i class="fas fa-check-circle"></i> ' + message).show();
+        
+        // Hide after 3 seconds
+        setTimeout(function() {
+            $errorDiv.fadeOut().removeClass('alert-success').addClass('alert-danger');
+        }, 3000);
+    }
+
+    function showBookingError(message, errors) {
+        let errorHtml = '<i class="fas fa-exclamation-triangle"></i> ' + message;
+        
+        if (errors && typeof errors === 'object') {
+            errorHtml += '<ul class="mt-2">';
+            for (let field in errors) {
+                if (Array.isArray(errors[field])) {
+                    errors[field].forEach(function(error) {
+                        errorHtml += '<li>' + error + '</li>';
+                    });
+                } else {
+                    errorHtml += '<li>' + errors[field] + '</li>';
+                }
+            }
+            errorHtml += '</ul>';
+        }
+        
+        showError(errorHtml);
+    }
+
+    function updateResultsCount() {
+        const count = filteredRetreats.length;
+        const text = count === 1 ? `${count} retreat found` : `${count} retreats found`;
+        $('#retreat-count').text(text);
+    }
+
+    function formatDate(dateString) {
+        const date = new Date(dateString);
+        return date.toLocaleDateString('en-US', {
+            year: 'numeric',
+            month: 'short',
+            day: 'numeric'
+        });
+    }
+
+    function isValidPhoneNumber(phone) {
+        return /^\d{10}$/.test(phone.replace(/\D/g, ''));
+    }
+
+    function getStatusBadgeClass(status) {
+        const classes = {
+            'confirmed': 'success',
+            'pending': 'warning',
+            'cancelled': 'danger',
+            'completed': 'info'
+        };
+        return classes[status] || 'secondary';
+    }
+
+    function generateWhatsAppCommunityUrl(message = '') {
+        // Replace with your actual WhatsApp community link
+        const communityUrl = 'https://chat.whatsapp.com/your_community_invite_link';
+        
+        if (message) {
+            return communityUrl + '?text=' + encodeURIComponent(message);
+        }
+        
+        return communityUrl;
+    }
+
+    function debounce(func, wait) {
+        let timeout;
+        return function executedFunction(...args) {
+            const later = () => {
+                clearTimeout(timeout);
+                func(...args);
+            };
+            clearTimeout(timeout);
+            timeout = setTimeout(later, wait);
+        };
+    }
+
+    // Initialize form validation
+    function initializeFormValidation() {
+        // Add custom validation styles
+        const style = document.createElement('style');
+        style.textContent = `
+            .form-control.is-invalid {
+                border-color: #dc3545;
+                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
+            }
+            .invalid-feedback {
+                display: block;
+                width: 100%;
+                margin-top: 0.25rem;
+                font-size: 0.875em;
+                color: #dc3545;
+            }
+        `;
+        document.head.appendChild(style);
+    }
+
+})(jQuery);

```



MarkAsCompleted: 1 items
Now let me create the WordPress AJAX handlers to connect the frontend to the Laravel API:


**Diff:**
```diff
--- E:\laragon\www\knk\wp-content\themes\your-theme\includes\ajax-handlers.php
+++ E:\laragon\www\knk\wp-content\themes\your-theme\includes\ajax-handlers.php
@@ -0,0 +1,562 @@
+<?php
+/**
+ * AJAX Handlers for Retreat Booking System
+ * Handles WordPress AJAX requests and communicates with Laravel API
+ */
+
+// Include the API class
+require_once get_template_directory() . '/includes/class-retreat-api.php';
+
+// Hook AJAX actions for both logged-in and non-logged-in users
+add_action('wp_ajax_get_retreats', 'handle_get_retreats');
+add_action('wp_ajax_nopriv_get_retreats', 'handle_get_retreats');
+
+add_action('wp_ajax_get_retreat_details', 'handle_get_retreat_details');
+add_action('wp_ajax_nopriv_get_retreat_details', 'handle_get_retreat_details');
+
+add_action('wp_ajax_submit_booking', 'handle_submit_booking');
+add_action('wp_ajax_nopriv_submit_booking', 'handle_submit_booking');
+
+add_action('wp_ajax_get_booking', 'handle_get_booking');
+add_action('wp_ajax_nopriv_get_booking', 'handle_get_booking');
+
+add_action('wp_ajax_cancel_booking_participant', 'handle_cancel_booking_participant');
+add_action('wp_ajax_nopriv_cancel_booking_participant', 'handle_cancel_booking_participant');
+
+/**
+ * Handle get retreats AJAX request
+ */
+function handle_get_retreats() {
+    // Verify nonce
+    if (!wp_verify_nonce($_POST['nonce'], 'retreat_ajax_nonce')) {
+        wp_send_json_error(['message' => 'Security check failed']);
+    }
+
+    try {
+        $api = retreat_api();
+        $filters = [];
+
+        // Apply any filters if provided
+        if (isset($_POST['filters'])) {
+            $filters = sanitize_filters($_POST['filters']);
+        }
+
+        $result = $api->get_retreats($filters);
+
+        if ($result['success']) {
+            wp_send_json_success([
+                'retreats' => $result['retreats'],
+                'count' => $result['count']
+            ]);
+        } else {
+            wp_send_json_error([
+                'message' => $result['message'],
+                'error_code' => $result['error_code']
+            ]);
+        }
+
+    } catch (Exception $e) {
+        error_log('Retreat AJAX Error (get_retreats): ' . $e->getMessage());
+        wp_send_json_error(['message' => 'An unexpected error occurred']);
+    }
+}
+
+/**
+ * Handle get retreat details AJAX request
+ */
+function handle_get_retreat_details() {
+    // Verify nonce
+    if (!wp_verify_nonce($_POST['nonce'], 'retreat_ajax_nonce')) {
+        wp_send_json_error(['message' => 'Security check failed']);
+    }
+
+    $retreat_id = intval($_POST['retreat_id']);
+    
+    if (!$retreat_id) {
+        wp_send_json_error(['message' => 'Invalid retreat ID']);
+    }
+
+    try {
+        $api = retreat_api();
+        $result = $api->get_retreat($retreat_id);
+
+        if ($result['success']) {
+            wp_send_json_success(['retreat' => $result['retreat']]);
+        } else {
+            wp_send_json_error([
+                'message' => $result['message'],
+                'error_code' => $result['error_code']
+            ]);
+        }
+
+    } catch (Exception $e) {
+        error_log('Retreat AJAX Error (get_retreat_details): ' . $e->getMessage());
+        wp_send_json_error(['message' => 'An unexpected error occurred']);
+    }
+}
+
+/**
+ * Handle submit booking AJAX request
+ */
+function handle_submit_booking() {
+    // Verify nonce
+    if (!wp_verify_nonce($_POST['nonce'], 'retreat_ajax_nonce')) {
+        wp_send_json_error(['message' => 'Security check failed']);
+    }
+
+    if (!isset($_POST['booking_data'])) {
+        wp_send_json_error(['message' => 'No booking data provided']);
+    }
+
+    try {
+        $booking_data = $_POST['booking_data'];
+        
+        // Sanitize booking data
+        $sanitized_data = sanitize_booking_data($booking_data);
+        
+        // Validate booking data
+        $validation_result = validate_booking_data($sanitized_data);
+        if (!$validation_result['valid']) {
+            wp_send_json_error([
+                'message' => 'Validation failed',
+                'errors' => $validation_result['errors']
+            ]);
+        }
+
+        $api = retreat_api();
+        $result = $api->create_booking($sanitized_data);
+
+        if ($result['success']) {
+            wp_send_json_success(['booking' => $result['booking']]);
+        } else {
+            wp_send_json_error([
+                'message' => $result['message'],
+                'error_code' => $result['error_code'],
+                'errors' => $result['errors']
+            ], $result['status_code'] ?? 400);
+        }
+
+    } catch (Exception $e) {
+        error_log('Retreat AJAX Error (submit_booking): ' . $e->getMessage());
+        wp_send_json_error(['message' => 'An unexpected error occurred']);
+    }
+}
+
+/**
+ * Handle get booking AJAX request
+ */
+function handle_get_booking() {
+    // Verify nonce
+    if (!wp_verify_nonce($_POST['nonce'], 'retreat_ajax_nonce')) {
+        wp_send_json_error(['message' => 'Security check failed']);
+    }
+
+    $booking_id = sanitize_text_field($_POST['booking_id']);
+    $whatsapp_number = sanitize_text_field($_POST['whatsapp_number']);
+
+    if (!$booking_id || !$whatsapp_number) {
+        wp_send_json_error(['message' => 'Booking ID and WhatsApp number are required']);
+    }
+
+    // Validate WhatsApp number format
+    if (!RetreatAPI::validate_phone($whatsapp_number)) {
+        wp_send_json_error(['message' => 'Invalid WhatsApp number format']);
+    }
+
+    try {
+        $api = retreat_api();
+        $result = $api->get_booking($booking_id, $whatsapp_number);
+
+        if ($result['success']) {
+            wp_send_json_success(['booking' => $result['booking']]);
+        } else {
+            wp_send_json_error([
+                'message' => $result['message'],
+                'error_code' => $result['error_code']
+            ]);
+        }
+
+    } catch (Exception $e) {
+        error_log('Retreat AJAX Error (get_booking): ' . $e->getMessage());
+        wp_send_json_error(['message' => 'An unexpected error occurred']);
+    }
+}
+
+/**
+ * Handle cancel booking participant AJAX request
+ */
+function handle_cancel_booking_participant() {
+    // Verify nonce
+    if (!wp_verify_nonce($_POST['nonce'], 'retreat_ajax_nonce')) {
+        wp_send_json_error(['message' => 'Security check failed']);
+    }
+
+    $booking_id = sanitize_text_field($_POST['booking_id']);
+    $serial_number = intval($_POST['serial_number']);
+
+    if (!$booking_id || !$serial_number) {
+        wp_send_json_error(['message' => 'Booking ID and serial number are required']);
+    }
+
+    try {
+        $api = retreat_api();
+        $result = $api->cancel_booking($booking_id, $serial_number);
+
+        if ($result['success']) {
+            wp_send_json_success(['cancellation' => $result['cancellation']]);
+        } else {
+            wp_send_json_error([
+                'message' => $result['message'],
+                'error_code' => $result['error_code']
+            ]);
+        }
+
+    } catch (Exception $e) {
+        error_log('Retreat AJAX Error (cancel_booking_participant): ' . $e->getMessage());
+        wp_send_json_error(['message' => 'An unexpected error occurred']);
+    }
+}
+
+/**
+ * Sanitize filters array
+ */
+function sanitize_filters($filters) {
+    $sanitized = [];
+    
+    $allowed_filters = ['date', 'criteria', 'search', 'category_id'];
+    
+    foreach ($filters as $key => $value) {
+        if (in_array($key, $allowed_filters)) {
+            switch ($key) {
+                case 'date':
+                    $sanitized[$key] = sanitize_text_field($value);
+                    break;
+                case 'criteria':
+                    $allowed_criteria = ['male_only', 'female_only', 'priests_only', 'sisters_only', 'youth_only', 'children', 'no_criteria'];
+                    if (in_array($value, $allowed_criteria)) {
+                        $sanitized[$key] = $value;
+                    }
+                    break;
+                case 'search':
+                    $sanitized[$key] = sanitize_text_field($value);
+                    break;
+                case 'category_id':
+                    $sanitized[$key] = intval($value);
+                    break;
+            }
+        }
+    }
+    
+    return $sanitized;
+}
+
+/**
+ * Sanitize booking data
+ */
+function sanitize_booking_data($booking_data) {
+    $sanitized = [];
+    
+    // Sanitize retreat_id
+    $sanitized['retreat_id'] = intval($booking_data['retreat_id']);
+    
+    // Sanitize participants array
+    $sanitized['participants'] = [];
+    
+    if (isset($booking_data['participants']) && is_array($booking_data['participants'])) {
+        foreach ($booking_data['participants'] as $participant) {
+            $sanitized_participant = [
+                'firstname' => sanitize_text_field($participant['firstname']),
+                'lastname' => sanitize_text_field($participant['lastname']),
+                'whatsapp_number' => preg_replace('/[^0-9]/', '', $participant['whatsapp_number']),
+                'age' => intval($participant['age']),
+                'email' => sanitize_email($participant['email']),
+                'address' => sanitize_textarea_field($participant['address']),
+                'city' => sanitize_text_field($participant['city']),
+                'state' => sanitize_text_field($participant['state']),
+                'gender' => sanitize_text_field($participant['gender']),
+                'emergency_contact_name' => sanitize_text_field($participant['emergency_contact_name']),
+                'emergency_contact_phone' => sanitize_text_field($participant['emergency_contact_phone'])
+            ];
+            
+            // Optional fields
+            if (isset($participant['diocese'])) {
+                $sanitized_participant['diocese'] = sanitize_text_field($participant['diocese']);
+            }
+            if (isset($participant['parish'])) {
+                $sanitized_participant['parish'] = sanitize_text_field($participant['parish']);
+            }
+            if (isset($participant['congregation'])) {
+                $sanitized_participant['congregation'] = sanitize_text_field($participant['congregation']);
+            }
+            if (isset($participant['special_remarks'])) {
+                $sanitized_participant['special_remarks'] = sanitize_textarea_field($participant['special_remarks']);
+            }
+            
+            $sanitized['participants'][] = $sanitized_participant;
+        }
+    }
+    
+    return $sanitized;
+}
+
+/**
+ * Validate booking data
+ */
+function validate_booking_data($booking_data) {
+    $errors = [];
+    
+    // Validate retreat_id
+    if (!$booking_data['retreat_id']) {
+        $errors['retreat_id'] = 'Retreat ID is required';
+    }
+    
+    // Validate participants
+    if (!isset($booking_data['participants']) || empty($booking_data['participants'])) {
+        $errors['participants'] = 'At least one participant is required';
+    } elseif (count($booking_data['participants']) > 4) {
+        $errors['participants'] = 'Maximum 4 participants allowed';
+    } else {
+        foreach ($booking_data['participants'] as $index => $participant) {
+            $participant_errors = validate_participant_data($participant, $index);
+            if (!empty($participant_errors)) {
+                $errors["participant_{$index}"] = $participant_errors;
+            }
+        }
+    }
+    
+    return [
+        'valid' => empty($errors),
+        'errors' => $errors
+    ];
+}
+
+/**
+ * Validate individual participant data
+ */
+function validate_participant_data($participant, $index = 0) {
+    $errors = [];
+    
+    // Required fields
+    $required_fields = [
+        'firstname' => 'First name is required',
+        'lastname' => 'Last name is required',
+        'whatsapp_number' => 'WhatsApp number is required',
+        'age' => 'Age is required',
+        'email' => 'Email address is required',
+        'gender' => 'Gender is required'
+    ];
+    
+    // Additional required fields for primary participant
+    if ($index === 0) {
+        $required_fields = array_merge($required_fields, [
+            'address' => 'Address is required',
+            'city' => 'City is required',
+            'state' => 'State is required',
+            'emergency_contact_name' => 'Emergency contact name is required',
+            'emergency_contact_phone' => 'Emergency contact phone is required'
+        ]);
+    }
+    
+    // Check required fields
+    foreach ($required_fields as $field => $message) {
+        if (!isset($participant[$field]) || empty(trim($participant[$field]))) {
+            $errors[$field] = $message;
+        }
+    }
+    
+    // Validate specific field formats
+    if (isset($participant['whatsapp_number']) && !empty($participant['whatsapp_number'])) {
+        if (!RetreatAPI::validate_phone($participant['whatsapp_number'])) {
+            $errors['whatsapp_number'] = 'WhatsApp number must be exactly 10 digits';
+        }
+    }
+    
+    if (isset($participant['email']) && !empty($participant['email'])) {
+        if (!is_email($participant['email'])) {
+            $errors['email'] = 'Please enter a valid email address';
+        }
+    }
+    
+    if (isset($participant['age']) && !empty($participant['age'])) {
+        $age = intval($participant['age']);
+        if ($age < 1 || $age > 120) {
+            $errors['age'] = 'Age must be between 1 and 120';
+        }
+    }
+    
+    if (isset($participant['gender']) && !empty($participant['gender'])) {
+        $allowed_genders = ['male', 'female', 'other'];
+        if (!in_array($participant['gender'], $allowed_genders)) {
+            $errors['gender'] = 'Please select a valid gender';
+        }
+    }
+    
+    return $errors;
+}
+
+/**
+ * Log API communication for debugging
+ */
+function log_api_communication($endpoint, $request_data, $response) {
+    if (WP_DEBUG && WP_DEBUG_LOG) {
+        error_log(sprintf(
+            'Retreat API Call: %s | Request: %s | Response: %s',
+            $endpoint,
+            json_encode($request_data),
+            json_encode($response)
+        ));
+    }
+}
+
+/**
+ * Check API connection status
+ */
+function check_retreat_api_connection() {
+    try {
+        $api = retreat_api();
+        return $api->test_connection();
+    } catch (Exception $e) {
+        error_log('Retreat API Connection Error: ' . $e->getMessage());
+        return false;
+    }
+}
+
+/**
+ * Get retreat API status for admin
+ */
+function get_retreat_api_status() {
+    $status = [
+        'connected' => false,
+        'message' => 'Not connected',
+        'last_check' => null
+    ];
+    
+    try {
+        $api = retreat_api();
+        $connected = $api->test_connection();
+        
+        $status['connected'] = $connected;
+        $status['message'] = $connected ? 'Connected successfully' : 'Connection failed';
+        $status['last_check'] = current_time('mysql');
+        
+    } catch (Exception $e) {
+        $status['message'] = 'Error: ' . $e->getMessage();
+        $status['last_check'] = current_time('mysql');
+    }
+    
+    return $status;
+}
+
+/**
+ * Admin AJAX handler to test API connection
+ */
+add_action('wp_ajax_test_retreat_api_connection', function() {
+    if (!current_user_can('manage_options')) {
+        wp_send_json_error(['message' => 'Permission denied']);
+    }
+    
+    if (!wp_verify_nonce($_POST['nonce'], 'retreat_admin_nonce')) {
+        wp_send_json_error(['message' => 'Security check failed']);
+    }
+    
+    $status = get_retreat_api_status();
+    
+    if ($status['connected']) {
+        wp_send_json_success($status);
+    } else {
+        wp_send_json_error($status);
+    }
+});
+
+/**
+ * Send JSON response with proper HTTP status code
+ */
+function wp_send_json_error($data = null, $status_code = 400) {
+    http_response_code($status_code);
+    wp_send_json([
+        'success' => false,
+        'data' => $data
+    ]);
+}
+
+/**
+ * Add admin notice if API is not configured
+ */
+add_action('admin_notices', function() {
+    if (!defined('RETREAT_API_URL') || !defined('RETREAT_API_KEY')) {
+        echo '<div class="notice notice-warning is-dismissible">';
+        echo '<p><strong>Retreat Booking:</strong> API configuration is incomplete. Please set RETREAT_API_URL and RETREAT_API_KEY constants in your wp-config.php file.</p>';
+        echo '</div>';
+    }
+});
+
+/**
+ * Add settings page for retreat API configuration
+ */
+add_action('admin_menu', function() {
+    add_options_page(
+        'Retreat Booking Settings',
+        'Retreat Booking',
+        'manage_options',
+        'retreat-booking-settings',
+        'retreat_booking_settings_page'
+    );
+});
+
+/**
+ * Render retreat booking settings page
+ */
+function retreat_booking_settings_page() {
+    if (isset($_POST['test_connection'])) {
+        $status = get_retreat_api_status();
+        $test_message = $status['connected'] 
+            ? '<div class="notice notice-success"><p>API connection successful!</p></div>'
+            : '<div class="notice notice-error"><p>API connection failed: ' . $status['message'] . '</p></div>';
+    }
+    ?>
+    <div class="wrap">
+        <h1>Retreat Booking Settings</h1>
+        
+        <?php if (isset($test_message)) echo $test_message; ?>
+        
+        <div class="card">
+            <h2>API Configuration</h2>
+            <table class="form-table">
+                <tr>
+                    <th scope="row">API URL</th>
+                    <td>
+                        <code><?php echo defined('RETREAT_API_URL') ? RETREAT_API_URL : 'Not configured'; ?></code>
+                        <p class="description">Set RETREAT_API_URL in wp-config.php</p>
+                    </td>
+                </tr>
+                <tr>
+                    <th scope="row">API Key</th>
+                    <td>
+                        <code><?php echo defined('RETREAT_API_KEY') ? '***' . substr(RETREAT_API_KEY, -4) : 'Not configured'; ?></code>
+                        <p class="description">Set RETREAT_API_KEY in wp-config.php</p>
+                    </td>
+                </tr>
+            </table>
+            
+            <form method="post" action="">
+                <p class="submit">
+                    <input type="submit" name="test_connection" class="button-primary" value="Test API Connection">
+                </p>
+            </form>
+        </div>
+        
+        <div class="card">
+            <h2>Usage Instructions</h2>
+            <p>To display the retreat booking interface, use the following shortcode on any page:</p>
+            <code>[retreat_booking]</code>
+            
+            <p>Or create a page using the "Retreat Booking" template.</p>
+            
+            <h3>Configuration</h3>
+            <p>Add the following constants to your wp-config.php file:</p>
+            <pre><code>define('RETREAT_API_URL', 'http://your-laravel-app.com/api');
+define('RETREAT_API_KEY', 'your_api_key_here');</code></pre>
+        </div>
+    </div>
+    <?php
+}

```



MarkAsCompleted: 5 items
Now let me create the shortcode and integration file:


**Diff:**
```diff
--- E:\laragon\www\knk\wp-content\themes\your-theme\includes\shortcodes.php
+++ E:\laragon\www\knk\wp-content\themes\your-theme\includes\shortcodes.php
@@ -0,0 +1,619 @@
+<?php
+/**
+ * Shortcodes and Integration for Retreat Booking System
+ */
+
+// Include required files
+require_once get_template_directory() . '/includes/class-retreat-api.php';
+require_once get_template_directory() . '/includes/ajax-handlers.php';
+
+/**
+ * Main retreat booking shortcode
+ */
+function retreat_booking_shortcode($atts) {
+    $atts = shortcode_atts([
+        'view' => 'full',         // full, list, search
+        'limit' => 12,            // Number of retreats to show
+        'category' => '',         // Specific category
+        'featured_only' => false, // Show only featured retreats
+        'criteria' => '',         // Specific criteria filter
+        'show_filters' => true,   // Show filter options
+        'show_manage_button' => true // Show manage booking button
+    ], $atts);
+
+    // Enqueue required scripts and styles
+    enqueue_retreat_booking_assets();
+
+    ob_start();
+    
+    // Generate unique ID for this shortcode instance
+    $instance_id = 'retreat-booking-' . uniqid();
+    
+    ?>
+    <div id="<?php echo $instance_id; ?>" class="retreat-booking-container" 
+         data-view="<?php echo esc_attr($atts['view']); ?>"
+         data-limit="<?php echo esc_attr($atts['limit']); ?>"
+         data-category="<?php echo esc_attr($atts['category']); ?>"
+         data-featured-only="<?php echo esc_attr($atts['featured_only']); ?>"
+         data-criteria="<?php echo esc_attr($atts['criteria']); ?>">
+        
+        <!-- Loading Indicator -->
+        <div class="retreat-loading" style="display: none;">
+            <div class="spinner"></div>
+            <p>Loading retreats...</p>
+        </div>
+
+        <!-- Error Message -->
+        <div class="retreat-error-message alert alert-danger" style="display: none;"></div>
+
+        <?php if ($atts['view'] === 'full' && $atts['show_filters']): ?>
+        <!-- Filters Section -->
+        <div class="retreat-filters">
+            <div class="row">
+                <div class="col-md-3">
+                    <div class="filter-group">
+                        <label for="<?php echo $instance_id; ?>-date-filter">Date Range</label>
+                        <select id="<?php echo $instance_id; ?>-date-filter" class="form-control date-filter">
+                            <option value="">All Dates</option>
+                            <option value="this-month">This Month</option>
+                            <option value="next-month">Next Month</option>
+                            <option value="next-3-months">Next 3 Months</option>
+                        </select>
+                    </div>
+                </div>
+                <div class="col-md-3">
+                    <div class="filter-group">
+                        <label for="<?php echo $instance_id; ?>-criteria-filter">Retreat Type</label>
+                        <select id="<?php echo $instance_id; ?>-criteria-filter" class="form-control criteria-filter">
+                            <option value="">All Types</option>
+                            <option value="male_only">Men Only</option>
+                            <option value="female_only">Women Only</option>
+                            <option value="priests_only">Priests Only</option>
+                            <option value="sisters_only">Sisters Only</option>
+                            <option value="youth_only">Youth Only</option>
+                            <option value="children">Children</option>
+                            <option value="no_criteria">Open to All</option>
+                        </select>
+                    </div>
+                </div>
+                <div class="col-md-3">
+                    <div class="filter-group">
+                        <label for="<?php echo $instance_id; ?>-search-filter">Search</label>
+                        <input type="text" id="<?php echo $instance_id; ?>-search-filter" 
+                               class="form-control search-filter" placeholder="Search retreats...">
+                    </div>
+                </div>
+                <div class="col-md-3">
+                    <div class="filter-group">
+                        <label>&nbsp;</label>
+                        <button class="btn btn-secondary btn-block clear-filters">Clear Filters</button>
+                    </div>
+                </div>
+            </div>
+        </div>
+        <?php endif; ?>
+
+        <!-- Results Count -->
+        <div class="retreat-results-info">
+            <p class="retreat-count">Loading...</p>
+        </div>
+
+        <!-- Retreat Grid -->
+        <div class="retreat-grid">
+            <!-- Retreat cards will be loaded here -->
+        </div>
+
+        <!-- Pagination -->
+        <div class="retreat-pagination">
+            <!-- Pagination will be added here if needed -->
+        </div>
+
+        <?php if ($atts['show_manage_button']): ?>
+        <!-- Manage Booking Button -->
+        <div class="retreat-actions">
+            <button class="btn btn-outline-primary manage-booking-trigger">
+                <i class="fas fa-calendar-check"></i> Manage Your Booking
+            </button>
+        </div>
+        <?php endif; ?>
+    </div>
+
+    <!-- Include modals -->
+    <?php include_retreat_modals(); ?>
+
+    <script type="text/javascript">
+    jQuery(document).ready(function($) {
+        // Initialize this shortcode instance
+        window.initializeRetreatBookingInstance('<?php echo $instance_id; ?>', <?php echo json_encode($atts); ?>);
+    });
+    </script>
+
+    <?php
+    return ob_get_clean();
+}
+add_shortcode('retreat_booking', 'retreat_booking_shortcode');
+
+/**
+ * Simple retreat list shortcode
+ */
+function retreat_list_shortcode($atts) {
+    $atts = shortcode_atts([
+        'limit' => 6,
+        'category' => '',
+        'featured_only' => false,
+        'criteria' => '',
+        'show_details' => true
+    ], $atts);
+
+    $booking_atts = array_merge($atts, [
+        'view' => 'list',
+        'show_filters' => false,
+        'show_manage_button' => false
+    ]);
+
+    return retreat_booking_shortcode($booking_atts);
+}
+add_shortcode('retreat_list', 'retreat_list_shortcode');
+
+/**
+ * Retreat search shortcode
+ */
+function retreat_search_shortcode($atts) {
+    $atts = shortcode_atts([
+        'placeholder' => 'Search retreats...',
+        'button_text' => 'Search'
+    ], $atts);
+
+    enqueue_retreat_booking_assets();
+    
+    ob_start();
+    ?>
+    <div class="retreat-search-widget">
+        <form class="retreat-search-form" method="get">
+            <div class="input-group">
+                <input type="text" class="form-control" name="retreat_search" 
+                       placeholder="<?php echo esc_attr($atts['placeholder']); ?>"
+                       value="<?php echo esc_attr($_GET['retreat_search'] ?? ''); ?>">
+                <div class="input-group-append">
+                    <button class="btn btn-primary" type="submit">
+                        <?php echo esc_html($atts['button_text']); ?>
+                    </button>
+                </div>
+            </div>
+        </form>
+        
+        <?php if (!empty($_GET['retreat_search'])): ?>
+        <div class="retreat-search-results" data-search="<?php echo esc_attr($_GET['retreat_search']); ?>">
+            <!-- Search results will be loaded here -->
+        </div>
+        <?php endif; ?>
+    </div>
+    <?php
+    return ob_get_clean();
+}
+add_shortcode('retreat_search', 'retreat_search_shortcode');
+
+/**
+ * Booking management shortcode
+ */
+function retreat_booking_manage_shortcode($atts) {
+    $atts = shortcode_atts([
+        'title' => 'Manage Your Booking',
+        'show_title' => true
+    ], $atts);
+
+    enqueue_retreat_booking_assets();
+    
+    ob_start();
+    ?>
+    <div class="retreat-booking-manage-widget">
+        <?php if ($atts['show_title']): ?>
+        <h3><?php echo esc_html($atts['title']); ?></h3>
+        <?php endif; ?>
+        
+        <div class="booking-verification-form">
+            <div class="row">
+                <div class="col-md-6">
+                    <div class="form-group">
+                        <label for="manage-booking-id">Booking ID *</label>
+                        <input type="text" id="manage-booking-id" class="form-control" 
+                               placeholder="e.g., RB123" required>
+                    </div>
+                </div>
+                <div class="col-md-6">
+                    <div class="form-group">
+                        <label for="manage-whatsapp">WhatsApp Number *</label>
+                        <input type="tel" id="manage-whatsapp" class="form-control" 
+                               placeholder="Primary user's WhatsApp number" required>
+                    </div>
+                </div>
+            </div>
+            <button type="button" class="btn btn-primary verify-booking-widget-btn">
+                View Booking Details
+            </button>
+        </div>
+
+        <div class="booking-details-container" style="display: none;">
+            <!-- Booking details will be loaded here -->
+        </div>
+    </div>
+    <?php
+    return ob_get_clean();
+}
+add_shortcode('retreat_manage', 'retreat_booking_manage_shortcode');
+
+/**
+ * Retreat stats shortcode (for displaying statistics)
+ */
+function retreat_stats_shortcode($atts) {
+    $atts = shortcode_atts([
+        'show_total_retreats' => true,
+        'show_upcoming_retreats' => true,
+        'show_available_spots' => true,
+        'cache_duration' => 300 // 5 minutes
+    ], $atts);
+
+    $cache_key = 'retreat_stats_' . md5(serialize($atts));
+    $stats = get_transient($cache_key);
+
+    if ($stats === false) {
+        try {
+            $api = retreat_api();
+            $result = $api->get_retreats();
+            
+            if ($result['success']) {
+                $retreats = $result['retreats'];
+                $stats = [
+                    'total_retreats' => count($retreats),
+                    'upcoming_retreats' => count(array_filter($retreats, function($r) {
+                        return strtotime($r['start_date']) > time();
+                    })),
+                    'available_spots' => array_sum(array_column($retreats, 'available_spots'))
+                ];
+                
+                set_transient($cache_key, $stats, $atts['cache_duration']);
+            } else {
+                $stats = [
+                    'total_retreats' => 0,
+                    'upcoming_retreats' => 0,
+                    'available_spots' => 0
+                ];
+            }
+        } catch (Exception $e) {
+            $stats = [
+                'total_retreats' => 0,
+                'upcoming_retreats' => 0,
+                'available_spots' => 0
+            ];
+        }
+    }
+
+    ob_start();
+    ?>
+    <div class="retreat-stats-widget">
+        <div class="row">
+            <?php if ($atts['show_total_retreats']): ?>
+            <div class="col-md-4">
+                <div class="stat-item">
+                    <div class="stat-number"><?php echo $stats['total_retreats']; ?></div>
+                    <div class="stat-label">Total Retreats</div>
+                </div>
+            </div>
+            <?php endif; ?>
+
+            <?php if ($atts['show_upcoming_retreats']): ?>
+            <div class="col-md-4">
+                <div class="stat-item">
+                    <div class="stat-number"><?php echo $stats['upcoming_retreats']; ?></div>
+                    <div class="stat-label">Upcoming Retreats</div>
+                </div>
+            </div>
+            <?php endif; ?>
+
+            <?php if ($atts['show_available_spots']): ?>
+            <div class="col-md-4">
+                <div class="stat-item">
+                    <div class="stat-number"><?php echo $stats['available_spots']; ?></div>
+                    <div class="stat-label">Available Spots</div>
+                </div>
+            </div>
+            <?php endif; ?>
+        </div>
+    </div>
+    <?php
+    return ob_get_clean();
+}
+add_shortcode('retreat_stats', 'retreat_stats_shortcode');
+
+/**
+ * Enqueue retreat booking assets
+ */
+function enqueue_retreat_booking_assets() {
+    static $assets_enqueued = false;
+    
+    if ($assets_enqueued) {
+        return;
+    }
+
+    // Enqueue jQuery and Bootstrap if not already loaded
+    wp_enqueue_script('jquery');
+    
+    // Check if Bootstrap is already loaded
+    if (!wp_script_is('bootstrap', 'enqueued')) {
+        wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
+        wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', ['jquery'], '4.5.2', true);
+    }
+
+    // FontAwesome icons
+    if (!wp_style_is('font-awesome', 'enqueued')) {
+        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
+    }
+
+    // Mustache.js for templating
+    wp_enqueue_script('mustache-js', 'https://cdnjs.cloudflare.com/ajax/libs/mustache.js/4.2.0/mustache.min.js', [], '4.2.0', true);
+
+    // Our custom scripts and styles
+    wp_enqueue_script('retreat-booking-js', get_template_directory_uri() . '/js/retreat-booking.js', ['jquery', 'mustache-js'], '1.0.0', true);
+    wp_enqueue_style('retreat-booking-css', get_template_directory_uri() . '/css/retreat-booking.css', [], '1.0.0');
+
+    // Localize script with AJAX URL and nonce
+    wp_localize_script('retreat-booking-js', 'retreat_ajax', [
+        'ajax_url' => admin_url('admin-ajax.php'),
+        'nonce' => wp_create_nonce('retreat_ajax_nonce'),
+        'base_url' => home_url()
+    ]);
+
+    $assets_enqueued = true;
+}
+
+/**
+ * Include retreat modals
+ */
+function include_retreat_modals() {
+    static $modals_included = false;
+    
+    if ($modals_included) {
+        return;
+    }
+    
+    ?>
+    <!-- Retreat Details Modal -->
+    <div id="retreat-details-modal" class="modal retreat-modal" tabindex="-1" role="dialog">
+        <div class="modal-dialog modal-lg" role="document">
+            <div class="modal-content">
+                <div class="modal-header">
+                    <h5 class="modal-title">Retreat Details</h5>
+                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
+                        <span aria-hidden="true">&times;</span>
+                    </button>
+                </div>
+                <div class="modal-body">
+                    <div class="retreat-details-content">
+                        <!-- Content loaded via JavaScript -->
+                    </div>
+                </div>
+                <div class="modal-footer">
+                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
+                    <button type="button" class="btn btn-primary book-retreat-btn">Book This Retreat</button>
+                </div>
+            </div>
+        </div>
+    </div>
+
+    <!-- Booking Form Modal -->
+    <div id="booking-form-modal" class="modal retreat-modal" tabindex="-1" role="dialog">
+        <div class="modal-dialog modal-xl" role="document">
+            <div class="modal-content">
+                <div class="modal-header">
+                    <h5 class="modal-title">Book Your Retreat</h5>
+                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
+                        <span aria-hidden="true">&times;</span>
+                    </button>
+                </div>
+                <div class="modal-body">
+                    <div class="booking-form-content">
+                        <!-- Content loaded via JavaScript -->
+                    </div>
+                </div>
+            </div>
+        </div>
+    </div>
+
+    <!-- Booking Success Modal -->
+    <div id="booking-success-modal" class="modal retreat-modal" tabindex="-1" role="dialog">
+        <div class="modal-dialog modal-lg" role="document">
+            <div class="modal-content">
+                <div class="modal-header bg-success text-white">
+                    <h5 class="modal-title">Booking Successful!</h5>
+                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
+                        <span aria-hidden="true">&times;</span>
+                    </button>
+                </div>
+                <div class="modal-body">
+                    <div class="booking-success-content">
+                        <!-- Content loaded via JavaScript -->
+                    </div>
+                </div>
+                <div class="modal-footer">
+                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
+                </div>
+            </div>
+        </div>
+    </div>
+
+    <!-- Manage Booking Modal -->
+    <div id="manage-booking-modal" class="modal retreat-modal" tabindex="-1" role="dialog">
+        <div class="modal-dialog modal-lg" role="document">
+            <div class="modal-content">
+                <div class="modal-header">
+                    <h5 class="modal-title">Manage Your Booking</h5>
+                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
+                        <span aria-hidden="true">&times;</span>
+                    </button>
+                </div>
+                <div class="modal-body">
+                    <div class="manage-booking-content">
+                        <!-- Content loaded via JavaScript -->
+                    </div>
+                </div>
+            </div>
+        </div>
+    </div>
+
+    <!-- Retreat Card Template -->
+    <script type="text/template" id="retreat-card-template">
+    <div class="retreat-card" data-retreat-id="{{retreat_id}}">
+        <div class="card">
+            {{#featured_image}}
+            <img class="card-img-top" src="{{featured_image}}" alt="{{retreat_name}}">
+            {{/featured_image}}
+            <div class="card-body">
+                <div class="retreat-header">
+                    <h5 class="card-title">{{retreat_name}}</h5>
+                    {{#is_featured}}
+                    <span class="badge badge-featured">Featured</span>
+                    {{/is_featured}}
+                </div>
+                
+                <div class="retreat-info">
+                    <p class="retreat-dates">
+                        <i class="fas fa-calendar"></i>
+                        {{start_date}} to {{end_date}}
+                    </p>
+                    
+                    <p class="retreat-location">
+                        <i class="fas fa-map-marker-alt"></i>
+                        {{location}}
+                    </p>
+                    
+                    <div class="retreat-pricing">
+                        {{#discount_price}}
+                        <span class="price-original">₹{{price}}</span>
+                        <span class="price-discounted">₹{{discount_price}}</span>
+                        {{/discount_price}}
+                        {{^discount_price}}
+                        <span class="price">₹{{price}}</span>
+                        {{/discount_price}}
+                    </div>
+                    
+                    <div class="retreat-availability">
+                        <span class="available-spots">{{available_spots}} spots available</span>
+                        <span class="criteria-badge">{{criteria_label}}</span>
+                    </div>
+                </div>
+                
+                <div class="card-actions">
+                    <button class="btn btn-outline-primary btn-sm view-details-btn" data-retreat-id="{{retreat_id}}">
+                        View Details
+                    </button>
+                    <button class="btn btn-primary btn-sm book-now-btn" data-retreat-id="{{retreat_id}}">
+                        Book Now
+                    </button>
+                </div>
+            </div>
+        </div>
+    </div>
+    </script>
+    <?php
+    
+    $modals_included = true;
+}
+
+/**
+ * Add retreat booking to main query (for archive pages)
+ */
+function modify_main_query_for_retreats($query) {
+    if (!is_admin() && $query->is_main_query()) {
+        if (is_page() && get_post_meta(get_queried_object_id(), '_wp_page_template', true) === 'template-retreat-booking.php') {
+            // This is a retreat booking page, no modification needed
+            return;
+        }
+    }
+}
+add_action('pre_get_posts', 'modify_main_query_for_retreats');
+
+/**
+ * Add custom body classes for retreat pages
+ */
+function add_retreat_body_classes($classes) {
+    if (is_page()) {
+        $template = get_post_meta(get_queried_object_id(), '_wp_page_template', true);
+        if ($template === 'template-retreat-booking.php') {
+            $classes[] = 'retreat-booking-page';
+        }
+    }
+    return $classes;
+}
+add_filter('body_class', 'add_retreat_body_classes');
+
+/**
+ * Register Gutenberg block for retreat booking
+ */
+function register_retreat_booking_block() {
+    if (function_exists('register_block_type')) {
+        register_block_type('retreat/booking', [
+            'render_callback' => 'retreat_booking_shortcode',
+            'attributes' => [
+                'view' => [
+                    'type' => 'string',
+                    'default' => 'full'
+                ],
+                'limit' => [
+                    'type' => 'number',
+                    'default' => 12
+                ],
+                'showFilters' => [
+                    'type' => 'boolean',
+                    'default' => true
+                ]
+            ]
+        ]);
+    }
+}
+add_action('init', 'register_retreat_booking_block');
+
+/**
+ * Clear retreat cache when needed
+ */
+function clear_retreat_cache() {
+    delete_transient('retreat_stats_' . md5(serialize(['show_total_retreats' => true, 'show_upcoming_retreats' => true, 'show_available_spots' => true])));
+}
+
+/**
+ * Schedule cache clearing
+ */
+function schedule_retreat_cache_clear() {
+    if (!wp_next_scheduled('clear_retreat_cache')) {
+        wp_schedule_event(time(), 'hourly', 'clear_retreat_cache');
+    }
+}
+add_action('wp', 'schedule_retreat_cache_clear');
+add_action('clear_retreat_cache', 'clear_retreat_cache');
+
+/**
+ * Integration with WordPress theme
+ */
+function integrate_retreat_booking_with_theme() {
+    // Add theme support if needed
+    add_theme_support('post-thumbnails');
+    
+    // Enqueue theme compatibility styles
+    if (is_page() || has_shortcode(get_post()->post_content ?? '', 'retreat_booking')) {
+        enqueue_retreat_booking_assets();
+    }
+}
+add_action('wp_enqueue_scripts', 'integrate_retreat_booking_with_theme');
+
+/**
+ * Add SEO meta tags for retreat pages
+ */
+function add_retreat_seo_meta() {
+    if (is_page()) {
+        $template = get_post_meta(get_queried_object_id(), '_wp_page_template', true);
+        if ($template === 'template-retreat-booking.php') {
+            echo '<meta name="description" content="Book your spiritual retreat with Mount Carmel Retreat Centre. Find upcoming retreats, view details, and secure your spot for a transformative spiritual journey.">' . "\n";
+            echo '<meta name="keywords" content="retreat booking, spiritual retreat, Mount Carmel, religious retreat, meditation retreat">' . "\n";
+        }
+    }
+}
+add_action('wp_head', 'add_retreat_seo_meta');

```



Finally, let me create the CSS file for styling:


**Diff:**
```diff
--- E:\laragon\www\knk\wp-content\themes\your-theme\css\retreat-booking.css
+++ E:\laragon\www\knk\wp-content\themes\your-theme\css\retreat-booking.css
@@ -0,0 +1,1283 @@
+/**
+ * Retreat Booking System Styles
+ * Comprehensive CSS for retreat booking functionality
+ */
+
+/* ==========================================================================
+   Base Styles
+   ========================================================================== */
+
+.retreat-booking-container {
+    padding: 2rem 0;
+    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
+}
+
+/* ==========================================================================
+   Loading & Error States
+   ========================================================================== */
+
+.retreat-loading {
+    text-align: center;
+    padding: 3rem 1rem;
+    background: #f8f9fa;
+    border-radius: 8px;
+    margin: 2rem 0;
+}
+
+.retreat-loading .spinner {
+    border: 4px solid #e9ecef;
+    border-top: 4px solid #007bff;
+    border-radius: 50%;
+    width: 40px;
+    height: 40px;
+    animation: retreat-spin 1s linear infinite;
+    margin: 0 auto 1rem;
+}
+
+@keyframes retreat-spin {
+    0% { transform: rotate(0deg); }
+    100% { transform: rotate(360deg); }
+}
+
+.retreat-error-message {
+    margin: 1rem 0;
+    padding: 1rem 1.25rem;
+    border-radius: 6px;
+    font-weight: 500;
+}
+
+.retreat-error-message i {
+    margin-right: 0.5rem;
+}
+
+/* ==========================================================================
+   Filters Section
+   ========================================================================== */
+
+.retreat-filters {
+    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
+    padding: 2rem;
+    border-radius: 12px;
+    margin-bottom: 2rem;
+    border: 1px solid #dee2e6;
+    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
+}
+
+.retreat-filters .row {
+    margin: 0 -0.5rem;
+}
+
+.retreat-filters .col-md-3 {
+    padding: 0 0.5rem;
+}
+
+.filter-group {
+    margin-bottom: 1rem;
+}
+
+.filter-group label {
+    font-weight: 600;
+    color: #495057;
+    margin-bottom: 0.5rem;
+    display: block;
+    font-size: 0.9rem;
+    text-transform: uppercase;
+    letter-spacing: 0.5px;
+}
+
+.filter-group .form-control {
+    border-radius: 8px;
+    border: 1px solid #ced4da;
+    padding: 0.5rem 0.75rem;
+    transition: all 0.3s ease;
+    font-size: 0.95rem;
+}
+
+.filter-group .form-control:focus {
+    border-color: #007bff;
+    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
+}
+
+.clear-filters {
+    border-radius: 8px;
+    font-weight: 600;
+    text-transform: uppercase;
+    letter-spacing: 0.5px;
+    transition: all 0.3s ease;
+}
+
+.clear-filters:hover {
+    background-color: #5a6268;
+    border-color: #545b62;
+}
+
+/* ==========================================================================
+   Results Info
+   ========================================================================== */
+
+.retreat-results-info {
+    margin-bottom: 1.5rem;
+    padding: 0.75rem 0;
+    border-bottom: 2px solid #e9ecef;
+    display: flex;
+    justify-content: space-between;
+    align-items: center;
+}
+
+.retreat-count {
+    margin: 0;
+    font-weight: 600;
+    color: #495057;
+    font-size: 1.1rem;
+}
+
+/* ==========================================================================
+   Retreat Grid
+   ========================================================================== */
+
+.retreat-grid {
+    display: grid;
+    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
+    gap: 2rem;
+    margin-bottom: 3rem;
+}
+
+.retreat-card {
+    height: 100%;
+    transition: transform 0.3s ease, box-shadow 0.3s ease;
+}
+
+.retreat-card:hover {
+    transform: translateY(-8px);
+}
+
+.retreat-card .card {
+    height: 100%;
+    border: 1px solid #e3e6f0;
+    border-radius: 15px;
+    overflow: hidden;
+    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
+    transition: all 0.3s ease;
+    background: white;
+}
+
+.retreat-card .card:hover {
+    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
+    border-color: #007bff;
+}
+
+.retreat-card .card-img-top {
+    height: 220px;
+    object-fit: cover;
+    transition: transform 0.3s ease;
+}
+
+.retreat-card .card:hover .card-img-top {
+    transform: scale(1.05);
+}
+
+.retreat-card .card-body {
+    padding: 1.5rem;
+    display: flex;
+    flex-direction: column;
+    height: calc(100% - 220px);
+}
+
+/* ==========================================================================
+   Retreat Card Content
+   ========================================================================== */
+
+.retreat-header {
+    display: flex;
+    justify-content: space-between;
+    align-items: flex-start;
+    margin-bottom: 1rem;
+    gap: 1rem;
+}
+
+.retreat-header h5 {
+    color: #2c3e50;
+    font-weight: 700;
+    margin: 0;
+    flex: 1;
+    font-size: 1.25rem;
+    line-height: 1.3;
+}
+
+.badge-featured {
+    background: linear-gradient(135deg, #f39c12, #e67e22);
+    color: white;
+    padding: 0.4rem 0.8rem;
+    border-radius: 20px;
+    font-size: 0.7rem;
+    font-weight: 600;
+    text-transform: uppercase;
+    letter-spacing: 0.5px;
+    white-space: nowrap;
+    box-shadow: 0 2px 8px rgba(243,156,18,0.3);
+}
+
+.retreat-info {
+    flex: 1;
+    margin-bottom: 1.5rem;
+}
+
+.retreat-info p {
+    margin-bottom: 0.8rem;
+    color: #6c757d;
+    font-size: 0.95rem;
+    display: flex;
+    align-items: center;
+}
+
+.retreat-info i {
+    width: 18px;
+    margin-right: 0.7rem;
+    color: #007bff;
+    font-size: 0.9rem;
+    text-align: center;
+}
+
+/* ==========================================================================
+   Pricing
+   ========================================================================== */
+
+.retreat-pricing {
+    margin: 1.2rem 0;
+    padding: 1rem;
+    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
+    border-radius: 10px;
+    text-align: center;
+}
+
+.price-original {
+    text-decoration: line-through;
+    color: #6c757d;
+    font-size: 1rem;
+    margin-right: 0.8rem;
+    opacity: 0.8;
+}
+
+.price-discounted, 
+.price {
+    font-size: 1.4rem;
+    font-weight: 700;
+    color: #28a745;
+    position: relative;
+}
+
+.price-discounted::before {
+    content: '₹';
+    font-size: 1rem;
+    margin-right: 2px;
+}
+
+.price::before {
+    content: '₹';
+    font-size: 1.1rem;
+    margin-right: 2px;
+}
+
+/* ==========================================================================
+   Availability
+   ========================================================================== */
+
+.retreat-availability {
+    display: flex;
+    justify-content: space-between;
+    align-items: center;
+    margin: 1rem 0;
+    padding: 0.8rem;
+    background: #f8f9fa;
+    border-radius: 8px;
+    border-left: 4px solid #28a745;
+}
+
+.available-spots {
+    color: #28a745;
+    font-weight: 600;
+    font-size: 0.9rem;
+    display: flex;
+    align-items: center;
+}
+
+.available-spots::before {
+    content: '👥';
+    margin-right: 0.5rem;
+    font-size: 1rem;
+}
+
+.criteria-badge {
+    background: linear-gradient(135deg, #6c757d, #495057);
+    color: white;
+    padding: 0.3rem 0.6rem;
+    border-radius: 12px;
+    font-size: 0.75rem;
+    font-weight: 500;
+    text-transform: capitalize;
+    letter-spacing: 0.3px;
+}
+
+/* ==========================================================================
+   Card Actions
+   ========================================================================== */
+
+.card-actions {
+    display: flex;
+    gap: 0.8rem;
+    padding-top: 1rem;
+    border-top: 1px solid #e9ecef;
+    margin-top: auto;
+}
+
+.card-actions .btn {
+    flex: 1;
+    padding: 0.6rem 1rem;
+    font-weight: 600;
+    border-radius: 8px;
+    transition: all 0.3s ease;
+    text-transform: uppercase;
+    letter-spacing: 0.3px;
+    font-size: 0.85rem;
+}
+
+.view-details-btn {
+    background: transparent;
+    color: #007bff;
+    border: 2px solid #007bff;
+}
+
+.view-details-btn:hover {
+    background: #007bff;
+    color: white;
+    transform: translateY(-1px);
+}
+
+.book-now-btn {
+    background: linear-gradient(135deg, #007bff, #0056b3);
+    border: 2px solid #007bff;
+    color: white;
+}
+
+.book-now-btn:hover {
+    background: linear-gradient(135deg, #0056b3, #004085);
+    border-color: #004085;
+    transform: translateY(-1px);
+    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
+}
+
+/* ==========================================================================
+   No Results
+   ========================================================================== */
+
+.no-results {
+    text-align: center;
+    padding: 4rem 2rem;
+    background: #f8f9fa;
+    border-radius: 12px;
+    margin: 2rem 0;
+}
+
+.no-results h4 {
+    color: #6c757d;
+    margin-bottom: 1rem;
+    font-weight: 600;
+}
+
+.no-results p {
+    color: #868e96;
+    font-size: 1.1rem;
+}
+
+/* ==========================================================================
+   Action Buttons (Floating)
+   ========================================================================== */
+
+.retreat-actions {
+    position: fixed;
+    bottom: 2rem;
+    right: 2rem;
+    z-index: 1050;
+}
+
+.manage-booking-trigger {
+    background: linear-gradient(135deg, #6c757d, #495057);
+    border: none;
+    color: white;
+    padding: 0.8rem 1.5rem;
+    border-radius: 25px;
+    font-weight: 600;
+    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
+    transition: all 0.3s ease;
+    text-transform: uppercase;
+    letter-spacing: 0.5px;
+    font-size: 0.9rem;
+}
+
+.manage-booking-trigger:hover {
+    background: linear-gradient(135deg, #495057, #343a40);
+    transform: translateY(-2px);
+    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
+    color: white;
+}
+
+.manage-booking-trigger i {
+    margin-right: 0.5rem;
+    font-size: 1.1rem;
+}
+
+/* ==========================================================================
+   Modal Styles
+   ========================================================================== */
+
+.retreat-modal .modal-dialog {
+    margin: 1.75rem auto;
+}
+
+.retreat-modal .modal-content {
+    border-radius: 15px;
+    border: none;
+    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
+}
+
+.retreat-modal .modal-header {
+    background: linear-gradient(135deg, #007bff, #0056b3);
+    color: white;
+    border-radius: 15px 15px 0 0;
+    padding: 1.5rem 2rem;
+    border-bottom: none;
+}
+
+.retreat-modal .modal-header.bg-success {
+    background: linear-gradient(135deg, #28a745, #1e7e34) !important;
+}
+
+.retreat-modal .modal-title {
+    font-weight: 700;
+    font-size: 1.3rem;
+}
+
+.retreat-modal .modal-body {
+    padding: 2rem;
+}
+
+.retreat-modal .modal-footer {
+    padding: 1.5rem 2rem;
+    border-top: 1px solid #e9ecef;
+    border-radius: 0 0 15px 15px;
+}
+
+/* ==========================================================================
+   Booking Form Styles
+   ========================================================================== */
+
+.booking-form-container {
+    max-height: 70vh;
+    overflow-y: auto;
+    padding-right: 0.5rem;
+}
+
+.retreat-summary-header {
+    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
+    padding: 1.5rem;
+    border-radius: 10px;
+    margin-bottom: 2rem;
+    text-align: center;
+    border: 1px solid #dee2e6;
+}
+
+.retreat-summary-header h6 {
+    color: #2c3e50;
+    font-weight: 700;
+    font-size: 1.2rem;
+    margin-bottom: 0.5rem;
+}
+
+.form-section {
+    margin-bottom: 2.5rem;
+    padding: 1.5rem;
+    background: #f8f9fa;
+    border-radius: 12px;
+    border-left: 4px solid #007bff;
+}
+
+.form-section h5 {
+    color: #2c3e50;
+    font-weight: 700;
+    margin-bottom: 1.5rem;
+    display: flex;
+    align-items: center;
+}
+
+.form-section h5::before {
+    content: '';
+    width: 4px;
+    height: 20px;
+    background: #007bff;
+    margin-right: 0.75rem;
+    border-radius: 2px;
+}
+
+.additional-participant-form {
+    background: white;
+    padding: 1.5rem;
+    border-radius: 10px;
+    margin-bottom: 1.5rem;
+    border: 1px solid #e9ecef;
+    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
+}
+
+.additional-participant-form h6 {
+    color: #495057;
+    font-weight: 600;
+    margin-bottom: 1rem;
+    padding-bottom: 0.5rem;
+    border-bottom: 2px solid #e9ecef;
+}
+
+.form-group label {
+    font-weight: 600;
+    color: #495057;
+    margin-bottom: 0.5rem;
+    font-size: 0.9rem;
+}
+
+.form-control {
+    border-radius: 8px;
+    border: 1px solid #ced4da;
+    padding: 0.6rem 0.75rem;
+    transition: all 0.3s ease;
+    font-size: 0.95rem;
+}
+
+.form-control:focus {
+    border-color: #007bff;
+    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
+}
+
+.form-control.is-invalid {
+    border-color: #dc3545;
+    box-shadow: 0 0 0 0.2rem rgba(220,53,69,0.25);
+}
+
+.invalid-feedback {
+    display: block;
+    width: 100%;
+    margin-top: 0.25rem;
+    font-size: 0.875em;
+    color: #dc3545;
+    font-weight: 500;
+}
+
+/* ==========================================================================
+   Booking Summary
+   ========================================================================== */
+
+.booking-summary {
+    background: linear-gradient(135deg, #e9ecef, #f8f9fa);
+    padding: 1.5rem;
+    border-radius: 12px;
+    border: 1px solid #dee2e6;
+    margin-bottom: 2rem;
+}
+
+.booking-summary h6 {
+    color: #2c3e50;
+    font-weight: 700;
+    margin-bottom: 1rem;
+    font-size: 1.1rem;
+}
+
+.summary-row {
+    display: flex;
+    justify-content: space-between;
+    align-items: center;
+    padding: 0.5rem 0;
+    border-bottom: 1px solid #dee2e6;
+    font-size: 0.95rem;
+}
+
+.summary-row:last-child {
+    border-bottom: none;
+}
+
+.summary-row.total-row {
+    background: white;
+    padding: 1rem;
+    margin: 1rem -0.5rem -0.5rem;
+    border-radius: 8px;
+    border: 2px solid #007bff;
+    font-size: 1.1rem;
+}
+
+.summary-row.total-row strong {
+    color: #007bff;
+}
+
+/* ==========================================================================
+   Form Actions
+   ========================================================================== */
+
+.form-actions {
+    display: flex;
+    gap: 1rem;
+    justify-content: flex-end;
+    padding-top: 2rem;
+    border-top: 2px solid #e9ecef;
+}
+
+.form-actions .btn {
+    padding: 0.75rem 2rem;
+    font-weight: 600;
+    border-radius: 8px;
+    transition: all 0.3s ease;
+    text-transform: uppercase;
+    letter-spacing: 0.5px;
+    min-width: 120px;
+}
+
+.btn-loading {
+    display: inline-flex;
+    align-items: center;
+    gap: 0.5rem;
+}
+
+/* ==========================================================================
+   Success Content
+   ========================================================================== */
+
+.booking-success-content {
+    text-align: center;
+}
+
+.booking-success-content i.fa-check-circle {
+    color: #28a745;
+    animation: success-pulse 2s infinite;
+}
+
+@keyframes success-pulse {
+    0% { transform: scale(1); }
+    50% { transform: scale(1.1); }
+    100% { transform: scale(1); }
+}
+
+.booking-details {
+    text-align: left;
+    margin-top: 2rem;
+}
+
+.whatsapp-community {
+    background: #f8f9fa;
+    padding: 1.5rem;
+    border-radius: 12px;
+    border: 1px solid #dee2e6;
+    margin: 1.5rem 0;
+}
+
+.whatsapp-community h6 {
+    color: #25d366;
+    font-weight: 700;
+    margin-bottom: 1rem;
+    display: flex;
+    align-items: center;
+}
+
+.whatsapp-community h6::before {
+    content: '💬';
+    margin-right: 0.5rem;
+    font-size: 1.2rem;
+}
+
+.qr-code-section, 
+.direct-link-section {
+    padding: 1rem;
+}
+
+.qr-code-section img {
+    max-width: 150px;
+    height: auto;
+    border-radius: 8px;
+    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
+}
+
+.btn-success {
+    background: linear-gradient(135deg, #25d366, #128c7e);
+    border: none;
+    box-shadow: 0 4px 15px rgba(37,211,102,0.3);
+    transition: all 0.3s ease;
+}
+
+.btn-success:hover {
+    background: linear-gradient(135deg, #128c7e, #075e54);
+    transform: translateY(-1px);
+    box-shadow: 0 6px 20px rgba(37,211,102,0.4);
+}
+
+.next-steps ul {
+    list-style: none;
+    padding-left: 0;
+}
+
+.next-steps li {
+    padding: 0.5rem 0;
+    display: flex;
+    align-items: center;
+}
+
+.next-steps li::before {
+    content: '✓';
+    color: #28a745;
+    font-weight: bold;
+    margin-right: 0.75rem;
+    font-size: 1.1rem;
+}
+
+/* ==========================================================================
+   Booking Management
+   ========================================================================== */
+
+.booking-verification-form {
+    background: #f8f9fa;
+    padding: 2rem;
+    border-radius: 12px;
+    margin-bottom: 2rem;
+    border: 1px solid #dee2e6;
+}
+
+.booking-management {
+    background: white;
+    border-radius: 12px;
+    padding: 0;
+    overflow: hidden;
+}
+
+.booking-header {
+    background: linear-gradient(135deg, #007bff, #0056b3);
+    color: white;
+    padding: 1.5rem;
+    display: flex;
+    justify-content: space-between;
+    align-items: center;
+}
+
+.booking-header h5 {
+    margin: 0;
+    font-weight: 700;
+}
+
+.retreat-info-summary {
+    padding: 1.5rem;
+    border-bottom: 1px solid #e9ecef;
+    background: #f8f9fa;
+}
+
+.retreat-info-summary h6 {
+    color: #2c3e50;
+    font-weight: 700;
+    margin-bottom: 0.5rem;
+}
+
+.participants-list {
+    padding: 1.5rem;
+}
+
+.participants-list h6 {
+    color: #495057;
+    font-weight: 600;
+    margin-bottom: 1rem;
+}
+
+.table {
+    margin-bottom: 0;
+}
+
+.table th {
+    background: #f8f9fa;
+    color: #495057;
+    font-weight: 600;
+    border-top: none;
+    padding: 0.75rem;
+}
+
+.table td {
+    padding: 0.75rem;
+    vertical-align: middle;
+}
+
+.badge-primary {
+    background: linear-gradient(135deg, #007bff, #0056b3);
+    padding: 0.4rem 0.8rem;
+    border-radius: 12px;
+}
+
+.participant-checkbox {
+    transform: scale(1.2);
+}
+
+.bulk-actions {
+    padding: 1.5rem;
+    background: #f8f9fa;
+    border-top: 1px solid #e9ecef;
+}
+
+.booking-summary {
+    padding: 1.5rem;
+    background: #f8f9fa;
+}
+
+/* ==========================================================================
+   Retreat Stats Widget
+   ========================================================================== */
+
+.retreat-stats-widget {
+    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
+    padding: 2rem;
+    border-radius: 15px;
+    border: 1px solid #dee2e6;
+    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
+}
+
+.stat-item {
+    text-align: center;
+    padding: 1.5rem;
+    background: white;
+    border-radius: 12px;
+    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
+    transition: transform 0.3s ease;
+}
+
+.stat-item:hover {
+    transform: translateY(-2px);
+}
+
+.stat-number {
+    font-size: 3rem;
+    font-weight: 700;
+    color: #007bff;
+    margin-bottom: 0.5rem;
+    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
+}
+
+.stat-label {
+    font-size: 1rem;
+    color: #6c757d;
+    font-weight: 600;
+    text-transform: uppercase;
+    letter-spacing: 0.5px;
+}
+
+/* ==========================================================================
+   Search Widget
+   ========================================================================== */
+
+.retreat-search-widget {
+    margin: 2rem 0;
+}
+
+.retreat-search-form .input-group {
+    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
+    border-radius: 10px;
+    overflow: hidden;
+}
+
+.retreat-search-form .form-control {
+    border: none;
+    padding: 1rem 1.25rem;
+    font-size: 1.1rem;
+    border-radius: 0;
+}
+
+.retreat-search-form .btn {
+    border: none;
+    padding: 1rem 1.5rem;
+    font-weight: 600;
+    text-transform: uppercase;
+    letter-spacing: 0.5px;
+    border-radius: 0;
+}
+
+.retreat-search-results {
+    margin-top: 2rem;
+}
+
+/* ==========================================================================
+   Manage Booking Widget
+   ========================================================================== */
+
+.retreat-booking-manage-widget {
+    background: #f8f9fa;
+    padding: 2rem;
+    border-radius: 12px;
+    border: 1px solid #dee2e6;
+}
+
+.retreat-booking-manage-widget h3 {
+    color: #2c3e50;
+    font-weight: 700;
+    margin-bottom: 1.5rem;
+}
+
+.booking-details-container {
+    margin-top: 2rem;
+    background: white;
+    border-radius: 10px;
+    border: 1px solid #e9ecef;
+    overflow: hidden;
+}
+
+/* ==========================================================================
+   Responsive Design
+   ========================================================================== */
+
+@media (max-width: 1200px) {
+    .retreat-grid {
+        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
+        gap: 1.5rem;
+    }
+}
+
+@media (max-width: 992px) {
+    .retreat-grid {
+        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
+        gap: 1.5rem;
+    }
+
+    .retreat-filters {
+        padding: 1.5rem;
+    }
+
+    .retreat-filters .row {
+        margin: 0;
+    }
+
+    .retreat-filters .col-md-3 {
+        padding: 0.25rem 0;
+        margin-bottom: 1rem;
+    }
+
+    .retreat-modal .modal-body {
+        padding: 1.5rem;
+    }
+
+    .booking-form-container {
+        max-height: 60vh;
+    }
+}
+
+@media (max-width: 768px) {
+    .retreat-booking-container {
+        padding: 1rem 0;
+    }
+
+    .retreat-grid {
+        grid-template-columns: 1fr;
+        gap: 1.5rem;
+    }
+
+    .retreat-filters {
+        padding: 1rem;
+        margin-bottom: 1.5rem;
+    }
+
+    .retreat-card .card-body {
+        padding: 1.25rem;
+    }
+
+    .retreat-header {
+        flex-direction: column;
+        align-items: flex-start;
+        gap: 0.75rem;
+    }
+
+    .retreat-availability {
+        flex-direction: column;
+        align-items: flex-start;
+        gap: 0.5rem;
+    }
+
+    .card-actions {
+        flex-direction: column;
+        gap: 0.75rem;
+    }
+
+    .retreat-actions {
+        bottom: 1rem;
+        right: 1rem;
+    }
+
+    .manage-booking-trigger {
+        padding: 0.6rem 1.2rem;
+        font-size: 0.85rem;
+    }
+
+    .retreat-modal .modal-dialog {
+        margin: 0.5rem;
+        max-width: none;
+    }
+
+    .retreat-modal .modal-header,
+    .retreat-modal .modal-body,
+    .retreat-modal .modal-footer {
+        padding: 1rem;
+    }
+
+    .booking-form-container {
+        max-height: 50vh;
+        padding-right: 0;
+    }
+
+    .form-section {
+        padding: 1rem;
+        margin-bottom: 1.5rem;
+    }
+
+    .form-actions {
+        flex-direction: column;
+        gap: 0.75rem;
+    }
+
+    .form-actions .btn {
+        width: 100%;
+        min-width: none;
+    }
+
+    .booking-summary {
+        padding: 1rem;
+        margin-bottom: 1.5rem;
+    }
+
+    .summary-row.total-row {
+        margin: 0.75rem -0.25rem -0.25rem;
+        padding: 0.75rem;
+    }
+
+    .retreat-stats-widget {
+        padding: 1.5rem;
+    }
+
+    .stat-item {
+        padding: 1rem;
+        margin-bottom: 1rem;
+    }
+
+    .stat-number {
+        font-size: 2.5rem;
+    }
+
+    .booking-verification-form {
+        padding: 1.5rem;
+    }
+
+    .participants-list {
+        padding: 1rem;
+        overflow-x: auto;
+    }
+
+    .table {
+        font-size: 0.9rem;
+    }
+
+    .table th,
+    .table td {
+        padding: 0.5rem;
+    }
+}
+
+@media (max-width: 576px) {
+    .retreat-booking-container {
+        padding: 0.5rem 0;
+    }
+
+    .retreat-grid {
+        gap: 1rem;
+    }
+
+    .retreat-card .card-body {
+        padding: 1rem;
+    }
+
+    .retreat-header h5 {
+        font-size: 1.1rem;
+    }
+
+    .retreat-info p {
+        font-size: 0.9rem;
+    }
+
+    .price-discounted,
+    .price {
+        font-size: 1.2rem;
+    }
+
+    .retreat-filters {
+        padding: 0.75rem;
+    }
+
+    .filter-group {
+        margin-bottom: 0.75rem;
+    }
+
+    .retreat-modal .modal-header,
+    .retreat-modal .modal-body,
+    .retreat-modal .modal-footer {
+        padding: 0.75rem;
+    }
+
+    .retreat-modal .modal-title {
+        font-size: 1.1rem;
+    }
+
+    .booking-form-container {
+        max-height: 45vh;
+    }
+
+    .form-section {
+        padding: 0.75rem;
+        margin-bottom: 1rem;
+    }
+
+    .form-section h5 {
+        font-size: 1rem;
+    }
+
+    .additional-participant-form {
+        padding: 1rem;
+    }
+
+    .booking-summary {
+        padding: 0.75rem;
+    }
+
+    .retreat-stats-widget {
+        padding: 1rem;
+    }
+
+    .stat-number {
+        font-size: 2rem;
+    }
+
+    .stat-label {
+        font-size: 0.85rem;
+    }
+}
+
+/* ==========================================================================
+   Print Styles
+   ========================================================================== */
+
+@media print {
+    .retreat-actions,
+    .retreat-filters,
+    .card-actions,
+    .modal,
+    .btn {
+        display: none !important;
+    }
+
+    .retreat-grid {
+        display: block !important;
+    }
+
+    .retreat-card {
+        break-inside: avoid;
+        margin-bottom: 1rem;
+        border: 1px solid #ccc;
+        padding: 1rem;
+    }
+
+    .retreat-card .card {
+        box-shadow: none;
+        border: none;
+    }
+}
+
+/* ==========================================================================
+   Accessibility Improvements
+   ========================================================================== */
+
+.retreat-booking-container *:focus {
+    outline: 2px solid #007bff;
+    outline-offset: 2px;
+}
+
+.btn:focus {
+    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.5);
+}
+
+.sr-only {
+    position: absolute !important;
+    width: 1px !important;
+    height: 1px !important;
+    padding: 0 !important;
+    margin: -1px !important;
+    overflow: hidden !important;
+    clip: rect(0, 0, 0, 0) !important;
+    white-space: nowrap !important;
+    border: 0 !important;
+}
+
+/* ==========================================================================
+   Dark Mode Support (Optional)
+   ========================================================================== */
+
+@media (prefers-color-scheme: dark) {
+    .retreat-booking-container {
+        color: #f8f9fa;
+    }
+
+    .retreat-card .card {
+        background: #2c3e50;
+        border-color: #495057;
+        color: #f8f9fa;
+    }
+
+    .retreat-filters {
+        background: linear-gradient(135deg, #2c3e50, #34495e);
+        border-color: #495057;
+    }
+
+    .form-control {
+        background: #495057;
+        border-color: #6c757d;
+        color: #f8f9fa;
+    }
+
+    .form-control:focus {
+        background: #495057;
+        border-color: #007bff;
+        color: #f8f9fa;
+    }
+}
+
+/* ==========================================================================
+   Animation Classes
+   ========================================================================== */
+
+.fade-in {
+    animation: fadeIn 0.5s ease-in;
+}
+
+@keyframes fadeIn {
+    from { opacity: 0; transform: translateY(20px); }
+    to { opacity: 1; transform: translateY(0); }
+}
+
+.slide-up {
+    animation: slideUp 0.3s ease-out;
+}
+
+@keyframes slideUp {
+    from { transform: translateY(30px); opacity: 0; }
+    to { transform: translateY(0); opacity: 1; }
+}
+
+.bounce-in {
+    animation: bounceIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
+}
+
+@keyframes bounceIn {
+    0% { transform: scale(0.3); opacity: 0; }
+    50% { transform: scale(1.05); }
+    70% { transform: scale(0.9); }
+    100% { transform: scale(1); opacity: 1; }
+}

```



MarkAsCompleted: 2 items
Now let me create a comprehensive setup and configuration guide: