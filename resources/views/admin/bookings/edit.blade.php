@extends('admin.layouts.app')

@section('title', 'Edit Booking: ' . $booking->booking_id)

@push('styles')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<!-- Datepicker -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
<style>
    /* Improved Select2 Styling */
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .select2-container--bootstrap4 .select2-selection--single:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: 2.25rem;
    }
    
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        color: #495057;
        padding-left: 0;
    }
    
    .select2-container--bootstrap4 .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .select2-container--bootstrap4 .select2-results__option {
        padding: 0.5rem 1rem;
    }
    
    .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff;
        color: white;
    }
    
    .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    /* Participant Section Styling */
    .participant-section {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
        background-color: #f8f9fa;
    }
    .participant-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
    }
    .participant-title {
        font-weight: 600;
        margin: 0;
    }
    .remove-participant {
        cursor: pointer;
        color: #dc3545;
    }
    .add-participant {
        margin-bottom: 20px;
    }
    .flag-warning {
        border-left: 4px solid #ffc107;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Booking: {{ $booking->booking_id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST" id="booking-form">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-ban"></i> Validation Errors!</h5>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Retreat Information</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="retreat_id">Select Retreat <span class="text-danger">*</span></label>
                                    <select name="retreat_id" id="retreat_id" class="form-control custom-select" style="width: 100%;" required>
                                        <option value="">-- Select Retreat --</option>
                                        @foreach($retreats as $retreat)
                                            @php
                                                $startDate = $retreat->start_date->format('M d, Y');
                                                $endDate = $retreat->end_date->format('M d, Y');
                                                $dateRange = "($startDate - $endDate)";
                                                $selected = old('retreat_id', $booking->retreat_id) == $retreat->id ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $retreat->id }}" 
                                                data-criteria="{{ $retreat->criteria }}"
                                                {{ $selected }}>
                                                {{ $retreat->title }} {{ $dateRange }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted" id="retreat-criteria"></small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="special_remarks">Special Remarks</label>
                                    <textarea name="special_remarks" id="special_remarks" class="form-control" rows="2" placeholder="Any special instructions or remarks">{{ old('special_remarks', $booking->special_remarks) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Primary Participant -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h4>Primary Participant</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstname">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" value="{{ old('firstname', $booking->firstname) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" value="{{ old('lastname', $booking->lastname) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="whatsapp_number">WhatsApp Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">+91</span>
                                        </div>
                                        <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $booking->whatsapp_number) }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="age">Age <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="age" name="age" value="{{ old('age', $booking->age) }}" min="1" max="120" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="male" {{ old('gender', $booking->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $booking->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $booking->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="married">Marital Status</label>
                                    <select class="form-control" id="married" name="married">
                                        <option value="">-- Select Status --</option>
                                        <option value="yes" {{ old('married', $booking->married) == 'yes' ? 'selected' : '' }}>Married</option>
                                        <option value="no" {{ old('married', $booking->married) == 'no' ? 'selected' : '' }}>Unmarried</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $booking->email) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $booking->address) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $booking->city) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">State <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $booking->state) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="diocese">Diocese</label>
                                    <input type="text" class="form-control" id="diocese" name="diocese" value="{{ old('diocese', $booking->diocese) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="parish">Parish</label>
                                    <input type="text" class="form-control" id="parish" name="parish" value="{{ old('parish', $booking->parish) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="congregation">Congregation (For Priests/Sisters) <span class="text-danger d-none" id="congregation-required">*</span></label>
                                    <input type="text" class="form-control @error('congregation') is-invalid @enderror" id="congregation" name="congregation" value="{{ old('congregation', $booking->congregation) }}">
                                    @error('congregation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_name">Emergency Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $booking->emergency_contact_name) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_phone">Emergency Contact Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $booking->emergency_contact_phone) }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Participants -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <input type="hidden" id="max-additional-members" value="{{ config('bookings.max_additional_members', 3) }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4>Additional Participants <small class="text-muted">(Maximum {{ config('bookings.max_additional_members', 3) }})</small></h4>
                                    <button type="button" class="btn btn-sm btn-primary add-participant" id="add-participant">
                                        <i class="fas fa-plus"></i> Add Participant
                                    </button>
                                </div>
                                <hr>
                                <input type="hidden" name="additional_participants" id="additional_participants" value="{{ $allParticipants->count() - 1 }}">
                                <div id="participants-container">
                                    @php $participantCount = 1; @endphp
                                    @foreach($allParticipants as $participant)
                                        @if($participant->id !== $booking->id)
                                            <div class="participant-section mb-4" id="participant-{{ $participantCount }}" data-participant-index="{{ $participantCount }}">
                                                <input type="hidden" name="participants[{{ $participantCount }}][id]" value="{{ $participant->id }}">
                                                <div class="participant-header d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="participant-title m-0">Participant #{{ $participantCount + 1 }}</h5>
                                        <button type="button" class="btn btn-sm btn-danger remove-participant text-white" data-participant="{{ $participantCount }}">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </div>
                                                @if($errors->has("participants.{$participantCount}.criteria"))
                                                    <div class="alert alert-danger">
                                                        <i class="icon fas fa-exclamation-triangle"></i> Please fix the following errors:
                                                        <ul class="mb-0 mt-2">
                                                            <li>{{ $errors->first("participants.{$participantCount}.criteria") }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                                            <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>First Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="participants[{{ $participantCount }}][firstname]" value="{{ old('participants.' . $participantCount . '.firstname', $participant->firstname) }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Last Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="participants[{{ $participantCount }}][lastname]" value="{{ old('participants.' . $participantCount . '.lastname', $participant->lastname) }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>WhatsApp Number <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">+91</span>
                                                                </div>
                                                                <input type="text" class="form-control participant-whatsapp @error('participants.' . $participantCount . '.whatsapp_number') is-invalid @enderror" name="participants[{{ $participantCount }}][whatsapp_number]" value="{{ old('participants.' . $participantCount . '.whatsapp_number', $participant->whatsapp_number) }}" minlength="10" maxlength="10" pattern="[0-9]{10}" required>
                                                                @error('participants.' . $participantCount . '.whatsapp_number')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Age <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" name="participants[{{ $participantCount }}][age]" value="{{ old('participants.' . $participantCount . '.age', $participant->age) }}" min="1" max="120" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Gender <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="participants[{{ $participantCount }}][gender]" required>
                                                                <option value="">-- Select Gender --</option>
                                                                <option value="male" {{ old('participants.' . $participantCount . '.gender', $participant->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                                                <option value="female" {{ old('participants.' . $participantCount . '.gender', $participant->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                                                <option value="other" {{ old('participants.' . $participantCount . '.gender', $participant->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Marital Status</label>
                                                            <select class="form-control" name="participants[{{ $participantCount }}][married]">
                                                                <option value="">-- Select Status --</option>
                                                                <option value="yes" {{ old('participants.' . $participantCount . '.married', $participant->married) === 'yes' ? 'selected' : '' }}>Married</option>
                                                                <option value="no" {{ old('participants.' . $participantCount . '.married', $participant->married) === 'no' ? 'selected' : '' }}>Unmarried</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label>Congregation (For Priests/Sisters)</label>
                                                            <input type="text" class="form-control" name="participants[{{ $participantCount }}][congregation]" value="{{ old('participants.' . $participantCount . '.congregation', $participant->congregation) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Email Address <span class="text-danger">*</span></label>
                                                            <input type="email" class="form-control" name="participants[{{ $participantCount }}][email]" value="{{ old('participants.' . $participantCount . '.email', $participant->email) }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @php $participantCount++; @endphp
                                        @endif
                                    @endforeach
                                    
                                    {{-- Render newly added participants from old input (those without ID) --}}
                                    @php
                                        $oldParticipants = old('participants', []);
                                        $existingCount = $participantCount;
                                    @endphp
                                    
                                    @foreach($oldParticipants as $index => $oldParticipant)
                                        @if(!isset($oldParticipant['id']) && !empty($oldParticipant['firstname']))
                                            <div class="participant-section mb-4" id="participant-{{ $index }}" data-participant-index="{{ $index }}">
                                                <div class="participant-header d-flex justify-content-between align-items-center mb-3">
                                                    <h5 class="participant-title m-0">Participant #{{ $index + 2 }}</h5>
                                                    <button type="button" class="btn btn-sm btn-danger remove-participant text-white" data-participant="{{ $index }}">
                                                        <i class="fas fa-times"></i> Remove
                                                    </button>
                                                </div>
                                                @if($errors->has("participants.{$index}.criteria"))
                                                    <div class="alert alert-danger">
                                                        <i class="icon fas fa-exclamation-triangle"></i> Please fix the following errors:
                                                        <ul class="mb-0 mt-2">
                                                            <li>{{ $errors->first("participants.{$index}.criteria") }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>First Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="participants[{{ $index }}][firstname]" value="{{ $oldParticipant['firstname'] ?? '' }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Last Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="participants[{{ $index }}][lastname]" value="{{ $oldParticipant['lastname'] ?? '' }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>WhatsApp Number <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">+91</span>
                                                                </div>
                                                                <input type="text" class="form-control participant-whatsapp" name="participants[{{ $index }}][whatsapp_number]" value="{{ $oldParticipant['whatsapp_number'] ?? '' }}" minlength="10" maxlength="10" pattern="[0-9]{10}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Age <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" name="participants[{{ $index }}][age]" value="{{ $oldParticipant['age'] ?? '' }}" min="1" max="120" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Gender <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="participants[{{ $index }}][gender]" required>
                                                                <option value="">-- Select Gender --</option>
                                                                <option value="male" {{ ($oldParticipant['gender'] ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                                                                <option value="female" {{ ($oldParticipant['gender'] ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                                                                <option value="other" {{ ($oldParticipant['gender'] ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Marital Status</label>
                                                            <select class="form-control" name="participants[{{ $index }}][married]">
                                                                <option value="">-- Select Status --</option>
                                                                <option value="yes" {{ ($oldParticipant['married'] ?? '') === 'yes' ? 'selected' : '' }}>Married</option>
                                                                <option value="no" {{ ($oldParticipant['married'] ?? '') === 'no' ? 'selected' : '' }}>Unmarried</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label>Congregation (For Priests/Sisters)</label>
                                                            <input type="text" class="form-control" name="participants[{{ $index }}][congregation]" value="{{ $oldParticipant['congregation'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Email Address <span class="text-danger">*</span></label>
                                                            <input type="email" class="form-control" name="participants[{{ $index }}][email]" value="{{ $oldParticipant['email'] ?? '' }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- Validation Messages -->
                        <div id="validation-messages" class="alert alert-danger d-none">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Validation Errors</h5>
                            <ul id="validation-errors">
                                <!-- Validation errors will be added here -->
                            </ul>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Booking
                        </button>
                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
@endsection

@push('scripts')
<!-- Select2 -->
<script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<!-- Datepicker -->
<script src="{{ asset('adminlte/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<!-- jquery-validation -->
<script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>

<script>
    $(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: '-- Select --',
            allowClear: true
        });
        
        // Style the select element
        $('#retreat_id').addClass('form-control-lg');
        
        // Format how options are displayed in the dropdown
        function formatRetreatOption(retreat) {
            if (!retreat.id) { return retreat.text; }
            
            var $container = $(
                '<div class="d-flex justify-content-between align-items-center">' +
                '   <span>' + retreat.text + '</span>' +
                '   <span class="badge badge-info ml-2">' + $(retreat.element).data('start-date') + ' to ' + $(retreat.element).data('end-date') + '</span>' +
                '</div>'
            );
            
            return $container;
        }
        
        // Format how the selected option is displayed
        function formatRetreatSelection(retreat) {
            if (!retreat.id) { return retreat.text; }
            return $('<span>').text(retreat.text).addClass('text-truncate d-inline-block');
        }
        
        // Initialize datepicker
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            todayHighlight: true
        });
        
        // Show retreat criteria when selected
        $('#retreat_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const criteria = selectedOption.data('criteria');
            const criteriaMap = {
                'male_only': 'Only for Males',
                'female_only': 'Only for Females',
                'priests_only': 'Only for Priests',
                'sisters_only': 'Only for Sisters',
                'youth_only': 'Only for Youth (16-30 years)',
                'children': 'Only for Children (15 years or below)',
                'no_criteria': 'Open to All'
            };
            
            $('#retreat-criteria').text('Criteria: ' + (criteriaMap[criteria] || 'Not specified'));
            
            // Handle congregation field requirement
            updateCongregationRequirement(criteria);
        });
        
        // Function to update congregation field requirement
        function updateCongregationRequirement(criteria) {
            const congregationField = $('#congregation');
            const congregationLabel = $('#congregation-required');
            
            if (criteria === 'priests_only' || criteria === 'sisters_only') {
                // Make congregation field required
                congregationField.attr('required', true);
                congregationLabel.removeClass('d-none');
                congregationField.closest('.form-group').addClass('required-field');
            } else {
                // Make congregation field optional
                congregationField.removeAttr('required');
                congregationLabel.addClass('d-none');
                congregationField.closest('.form-group').removeClass('required-field');
                
                // Remove any validation errors
                congregationField.removeClass('is-invalid');
                congregationField.next('.invalid-feedback').hide();
            }
        }
        
        // Initialize participant counter and max participants
        const maxParticipants = parseInt($('#max-additional-members').val());
        let participantCount = $('.participant-section').length;
        let nextParticipantNumber = participantCount + 1;
        
        // Update the additional_participants hidden input
        $('#additional_participants').val(participantCount);
        
        // Initialize existing participants
        $('.participant-section').each(function(index) {
            const partIndex = index + 1;
            const displayNumber = partIndex + 1; // Display as #2, #3, etc.
            
            // Update input names to ensure proper array indexing
            $(this).find('[name^="participants["]').each(function() {
                const name = $(this).attr('name').replace(/participants\[\d+\]/, 'participants[' + partIndex + ']');
                $(this).attr('name', name);
            });
            
            // Update participant title and ID
            $(this).attr('id', 'participant-' + partIndex);
            $(this).find('.participant-title').text('Participant #' + displayNumber);
            
            // Update remove button data attribute
            $(this).find('.remove-participant').attr('data-participant', partIndex);
        });
        
        // Update the hidden field with current count
        $('#additional_participants').val(participantCount);
        
        // Hide add button if we've reached max participants
        if (participantCount >= maxParticipants) {
            $('#add-participant').hide();
        }
        
        // Handle add participant button click
        $('#add-participant').on('click', function(e) {
            e.preventDefault();
            addParticipant();
        });
        
        // Handle add another participant button click
        $(document).on('click', '.add-another-participant', function() {
            addParticipant();
        });
        
        // Handle remove participant
        $(document).on('click', '.remove-participant', function() {
            const participantDiv = $(this).closest('.participant-section');
            const participantId = participantDiv.attr('id').replace('participant-', '');
            
            // Check if this is an existing participant (has data-participant-id)
            const participantDataId = participantDiv.data('participant-id');
            
            if (participantDataId) {
                // For existing participants, mark as deleted instead of removing
                participantDiv.find('input, select, textarea').prop('disabled', true);
                participantDiv.addClass('bg-light');
                participantDiv.prepend('<input type="hidden" name="deleted_participants[]" value="' + participantDataId + '">');
                participantDiv.hide('slow');
            } else {
                // For new participants, just remove the element
                participantDiv.next('.text-center').remove();
                participantDiv.remove();
            }
            
            // Update participant count
            participantCount--;
            $('#additional_participants').val(participantCount);
            
            // Update the numbering of remaining participants
            let visibleIndex = 0;
            $('.participant-section:visible').each(function() {
                visibleIndex++;
                const displayNumber = visibleIndex + 1; // Display as #2, #3, etc.
                $(this).attr('id', 'participant-' + visibleIndex);
                $(this).find('.participant-title').text('Participant #' + displayNumber);
                
                // Update input names
                $(this).find('[name^="participants["]').each(function() {
                    const name = $(this).attr('name').replace(/participants\[\d+\]/, 'participants[' + visibleIndex + ']');
                    $(this).attr('name', name);
                });
                
                // Update remove button data attribute
                $(this).find('.remove-participant').attr('data-participant', visibleIndex);
            });
            
            // Show add button if under max
            if (participantCount < maxParticipants) {
                $('#add-participant, .add-another-participant').prop('disabled', false);
                
                // Add 'Add Another' button if it doesn't exist
                if ($('.add-another-participant').length === 0) {
                    const addButtonHtml = `
                        <div class="text-center mb-4">
                            <button type="button" class="btn btn-outline-primary add-another-participant">
                                <i class="fas fa-plus"></i> Add Another Participant
                            </button>
                        </div>`;
                    $('#participants-container').append(addButtonHtml);
                }
            }
        });
        
        // Add participant function
        function addParticipant(participantData = null, index = null) {
            const currentCount = $('.participant-section').length;
            if (currentCount >= maxParticipants) {
                alert('Maximum of ' + maxParticipants + ' additional participants allowed.');
                return;
            }
            
            const partIndex = index !== null ? index : nextParticipantNumber;
            
            // Create participant HTML
            const participantHtml = `
                <div class="participant-section mb-4" id="participant-${partIndex}">
                    <input type="hidden" name="participants[${partIndex}][id]" value="${participantData ? participantData.id : ''}">
                    <div class="participant-header d-flex justify-content-between align-items-center mb-3">
                        <h5 class="participant-title m-0">Participant #${partIndex}</h5>
                        <button type="button" class="btn btn-sm btn-danger remove-participant text-white" data-participant="${partIndex}">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="participants[${partIndex}][firstname]" value="${participantData ? participantData.firstname : ''}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="participants[${partIndex}][lastname]" value="${participantData ? participantData.lastname : ''}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>WhatsApp Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+91</span>
                                    </div>
                                    <input type="text" class="form-control participant-whatsapp" name="participants[${partIndex}][whatsapp_number]" value="${participantData ? participantData.whatsapp_number : ''}" minlength="10" maxlength="10" pattern="[0-9]{10}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Age <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="participants[${partIndex}][age]" value="${participantData ? participantData.age : ''}" min="1" max="120" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gender <span class="text-danger">*</span></label>
                                <select class="form-control" name="participants[${partIndex}][gender]" required>
                                    <option value="">-- Select Gender --</option>
                                    <option value="male" ${participantData && participantData.gender === 'male' ? 'selected' : ''}>Male</option>
                                    <option value="female" ${participantData && participantData.gender === 'female' ? 'selected' : ''}>Female</option>
                                    <option value="other" ${participantData && participantData.gender === 'other' ? 'selected' : ''}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Marital Status</label>
                                <select class="form-control" name="participants[${partIndex}][married]">
                                    <option value="">-- Select Status --</option>
                                    <option value="yes" ${participantData && participantData.married === 'yes' ? 'selected' : ''}>Married</option>
                                    <option value="no" ${participantData && participantData.married === 'no' ? 'selected' : ''}>Unmarried</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Congregation (For Priests/Sisters)</label>
                                <input type="text" class="form-control" name="participants[${partIndex}][congregation]" value="${participantData ? participantData.congregation : ''}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="participants[${partIndex}][email]" value="${participantData ? participantData.email : ''}" required>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add participant HTML to container
            $('#participants-container').append(participantHtml);
            
            // Update participant count and next participant number
            participantCount++;
            nextParticipantNumber++;
            
            // Update the numbering of remaining participants
            let currentIndex = 0;
            $('.participant-section').each(function() {
                currentIndex++;
                const displayNumber = currentIndex + 1; // Display as #2, #3, etc.
                $(this).attr('id', 'participant-' + currentIndex);
                $(this).find('.participant-title').text('Participant #' + displayNumber);
                
                // Update input names
                $(this).find('[name^="participants["]').each(function() {
                    const name = $(this).attr('name').replace(/participants\[\d+\]/, 'participants[' + currentIndex + ']');
                    $(this).attr('name', name);
                });
                
                // Update remove button data attribute
                $(this).find('.remove-participant').attr('data-participant', currentIndex);
            });
            
            // Update additional participants hidden input
            $('#additional_participants').val(participantCount);
            
            // Hide add button if we've reached max participants
            if (participantCount >= maxParticipants) {
                $('#add-participant, .add-another-participant').prop('disabled', true);
            }
        }
        
        // Initialize retreat criteria display
        function updateRetreatCriteria() {
            const selectedOption = $('#retreat_id').find('option:selected');
            const criteria = selectedOption.data('criteria');
            const criteriaMap = {
                'male_only': 'Only for Males',
                'female_only': 'Only for Females',
                'priests_only': 'Only for Priests',
                'sisters_only': 'Only for Sisters',
                'youth_only': 'Only for Youth (16-30 years)',
                'children': 'Only for Children (15 years or below)',
                'no_criteria': 'Open to All'
            };
            
            $('#retreat-criteria').text('Criteria: ' + (criteriaMap[criteria] || 'Not specified'));
        }
        
        // Update criteria on page load if retreat is selected
        if ($('#retreat_id').val()) {
            updateRetreatCriteria();
        }
        
        // Update criteria when retreat changes
        $('#retreat_id').on('change', updateRetreatCriteria);
        
        // Initialize congregation field requirement on page load
        const initialCriteria = $('#retreat_id option:selected').data('criteria');
        if (initialCriteria) {
            updateCongregationRequirement(initialCriteria);
        }
        
        // Helper function to validate participant against criteria
        function validateParticipantCriteria(participant, criteria) {
            const errors = [];
            
            if (!criteria || criteria === 'no_criteria') {
                return errors;
            }
            
            switch (criteria) {
                case 'male_only':
                    if (participant.gender !== 'male') {
                        errors.push(`${participant.name}: Must be Male`);
                    }
                    break;
                    
                case 'female_only':
                    if (participant.gender !== 'female') {
                        errors.push(`${participant.name}: Must be Female`);
                    }
                    break;
                    
                case 'priests_only':
                    if (!participant.congregation) {
                        errors.push(`${participant.name}: Congregation is required for Priests`);
                    }
                    break;
                    
                case 'sisters_only':
                    if (participant.gender !== 'female') {
                        errors.push(`${participant.name}: Must be Female for Sisters retreat`);
                    }
                    if (!participant.congregation) {
                        errors.push(`${participant.name}: Congregation is required for Sisters`);
                    }
                    break;
                    
                case 'youth_only':
                    if (participant.age < 16 || participant.age > 30) {
                        errors.push(`${participant.name}: Age must be between 16-30 years for Youth retreat`);
                    }
                    break;
                    
                case 'children':
                    if (participant.age > 15) {
                        errors.push(`${participant.name}: Age must be 15 years or below for Children retreat`);
                    }
                    break;
            }
            
            return errors;
        }
        
        // Helper function to show validation errors
        function showValidationErrors(errors) {
            const $validationMessages = $('#validation-messages');
            const $validationList = $('#validation-errors');
            
            $validationList.empty();
            
            errors.forEach(function(error) {
                $validationList.append(`<li>${error}</li>`);
            });
            
            $validationMessages.removeClass('d-none');
            $validationMessages.removeClass('alert-warning').addClass('alert-danger');
            
            // Scroll to validation messages
            $('html, body').animate({
                scrollTop: $validationMessages.offset().top - 100
            }, 500);
        }
        
        // Add validation rules for dynamically added participant WhatsApp fields
        $(document).on('focus', '.participant-whatsapp', function() {
            const fieldName = $(this).attr('name');
            if (!$('#booking-form').validate().settings.rules[fieldName]) {
                $(this).rules('add', {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                    digits: true,
                    messages: {
                        required: 'Please enter WhatsApp number',
                        minlength: 'Please enter a valid 10-digit number',
                        maxlength: 'Please enter a valid 10-digit number',
                        digits: 'Please enter numbers only'
                    }
                });
            }
        });
        
        // Form validation
        $('#booking-form').validate({
            rules: {
                retreat_id: 'required',
                firstname: 'required',
                lastname: 'required',
                whatsapp_number: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                    digits: true
                },
                age: {
                    required: true,
                    min: 1,
                    max: 120
                },
                email: {
                    required: true,
                    email: true
                },
                address: 'required',
                gender: 'required',
                city: 'required',
                state: 'required',
                emergency_contact_name: 'required',
                emergency_contact_phone: {
                    required: true,
                    minlength: 10,
                    maxlength: 15,
                    digits: true
                }
            },
            messages: {
                retreat_id: 'Please select a retreat',
                firstname: 'Please enter first name',
                lastname: 'Please enter last name',
                whatsapp_number: {
                    required: 'Please enter WhatsApp number',
                    minlength: 'Please enter a valid 10-digit number',
                    maxlength: 'Please enter a valid 10-digit number',
                    digits: 'Please enter numbers only'
                },
                age: {
                    required: 'Please enter age',
                    min: 'Please enter a valid age',
                    max: 'Please enter a valid age'
                },
                email: {
                    required: 'Please enter email address',
                    email: 'Please enter a valid email address'
                },
                address: 'Please enter address',
                gender: 'Please select gender',
                city: 'Please enter city',
                state: 'Please enter state',
                emergency_contact_name: 'Please enter emergency contact name',
                emergency_contact_phone: {
                    required: 'Please enter emergency contact phone',
                    minlength: 'Please enter a valid phone number',
                    digits: 'Please enter numbers only'
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                // Check for validation errors
                const errors = [];
                
                // Check if retreat is selected
                const retreatId = $('#retreat_id').val();
                if (!retreatId) {
                    errors.push('Please select a retreat');
                    showValidationErrors(errors);
                    return false;
                }
                
                // Get retreat criteria
                const criteria = $('#retreat_id option:selected').data('criteria');
                
                // Validate primary participant
                const primaryParticipant = {
                    name: 'Primary Participant',
                    gender: $('#gender').val(),
                    age: parseInt($('#age').val()),
                    congregation: $('#congregation').val()?.trim() || ''
                };
                
                const primaryErrors = validateParticipantCriteria(primaryParticipant, criteria);
                if (primaryErrors.length > 0) {
                    errors.push(...primaryErrors);
                }
                
                // Validate all additional participants
                $('.participant-section:visible').each(function(index) {
                    const participantNum = index + 2; // Display as #2, #3, etc.
                    const participant = {
                        name: `Participant #${participantNum}`,
                        gender: $(this).find('select[name*="[gender]"]').val(),
                        age: parseInt($(this).find('input[name*="[age]"]').val()),
                        congregation: $(this).find('input[name*="[congregation]"]').val()?.trim() || ''
                    };
                    
                    const participantErrors = validateParticipantCriteria(participant, criteria);
                    if (participantErrors.length > 0) {
                        errors.push(...participantErrors);
                    }
                });
                
                // Show errors if any and prevent submission
                if (errors.length > 0) {
                    showValidationErrors(errors);
                    return false;
                }
                
                // All validations passed, submit the form
                form.submit();
            }
        });
    });
</script>
@endpush
