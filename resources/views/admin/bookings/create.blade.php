@extends('admin.layouts.app')

@section('title', 'Create New Booking')

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
                    <h3 class="card-title">Create New Booking</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <form action="{{ route('admin.bookings.store') }}" method="POST" id="booking-form">
                    @csrf
                    <div class="card-body">
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
                                                $selected = old('retreat_id') == $retreat->id ? 'selected' : '';
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
                                    <textarea class="form-control" id="special_remarks" name="special_remarks" rows="3">{{ old('special_remarks') }}</textarea>
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
                                    <input type="text" class="form-control" id="firstname" name="firstname" value="{{ old('firstname') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" value="{{ old('lastname') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="whatsapp_number">WhatsApp Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">+91</span>
                                        </div>
                                        <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="age">Age <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="age" name="age" min="1" max="120" value="{{ old('age') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">State <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="state" name="state" value="{{ old('state') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="diocese">Diocese</label>
                                    <input type="text" class="form-control" id="diocese" name="diocese" value="{{ old('diocese') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="parish">Parish</label>
                                    <input type="text" class="form-control" id="parish" name="parish" value="{{ old('parish') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="congregation">Congregation (For Priests/Sisters)</label>
                                    <input type="text" class="form-control" id="congregation" name="congregation" value="{{ old('congregation') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_name">Emergency Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_phone">Emergency Contact Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Add More Members Prompt -->
                        <div class="row mt-4" id="add-more-members-prompt">
                            <div class="col-md-12">
                                <input type="hidden" id="max-additional-members" value="{{ config('bookings.max_additional_members', 3) }}">
                                <div class="card card-info">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0">Would you like to add additional members to this booking? (Maximum {{ config('bookings.max_additional_members', 3) }} additional members allowed)</p>
                                            <button type="button" class="btn btn-primary" id="btn-yes-add-members">
                                                <i class="fas fa-plus"></i> Yes, Add Members
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Participants Section (Initially Hidden) -->
                        <div class="row mt-4 d-none" id="additional-participants-section">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4>Additional Participants <small class="text-muted">(Maximum {{ config('bookings.max_additional_members', 3) }})</small></h4>
                                </div>
                                <hr>
                                <input type="hidden" name="additional_participants" id="additional_participants" value="0">
                                <div id="participants-container">
                                    <!-- Additional participants will be added here -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Validation Messages -->
                        <div id="validation-messages" class="alert alert-warning d-none">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Validation Warnings</h5>
                            <ul id="validation-errors">
                                <!-- Validation errors will be added here -->
                            </ul>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Booking
                        </button>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-default">
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
<!-- Bootstrap Datepicker -->
<script src="{{ asset('adminlte/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<script>
    $(document).ready(function() {
        // Style the select element
        $('#retreat_id').addClass('form-control-lg');
        
        // Update criteria display when retreat is selected
        $('#retreat_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var criteria = selectedOption.data('criteria');
            var criteriaMap = {
                'male_only': 'Only for Males',
                'female_only': 'Only for Females',
                'priests_only': 'Only for Priests',
                'sisters_only': 'Only for Sisters',
                'youth_only': 'Only for Youth (18-35 years)',
                'children': 'Only for Children (below 18 years)',
                'no_criteria': 'Open to All'
            };
            
            $('#retreat-criteria').text('Criteria: ' + (criteriaMap[criteria] || 'Not specified'));
        });
        
        // Repopulate form with old input if validation fails
        @if(old('additional_participants', 0) > 0)
            // Show the participants container
            $('#participants-container').show();
            $('#add-more-members-prompt').hide();
            
            // Add participants based on old input
            const oldParticipants = @json(old('participants', []));
            oldParticipants.forEach(function(participant, index) {
                addParticipant(participant, index);
            });
        @endif
        
        // Initialize datepicker after all scripts are loaded
        $(window).on('load', function() {
            $('.datepicker').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                orientation: 'bottom auto'
            });
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
                'youth_only': 'Only for Youth (18-35 years)',
                'children': 'Only for Children (below 18 years)',
                'no_criteria': 'Open to All'
            };
            
            $('#retreat-criteria').text('Criteria: ' + (criteriaMap[criteria] || 'Not specified'));
        });
        
        // Add participant
        let participantCount = 0;
        const maxParticipants = parseInt($('#max-additional-members').val()); // Use dynamic value from config
        let nextParticipantNumber = 1;
        
        // Show/hide add more members section - using event delegation for dynamically added elements
        $(document).on('click', '#btn-yes-add-members', function(e) {
            e.preventDefault();
            $('#add-more-members-prompt').addClass('d-none');
            $('#additional-participants-section').removeClass('d-none');
            addParticipant(); // Add first participant
        });
        
        $('#btn-no-thanks').on('click', function() {
            $('#add-more-members-prompt').addClass('d-none');
        });
        
        function addParticipant(participantData = null, index = null) {
            const currentCount = $('.participant-section').length;
            if (currentCount >= maxParticipants) {
                alert('Maximum of ' + maxParticipants + ' additional participants allowed.');
                return;
            }
            
            const partIndex = index !== null ? index : nextParticipantNumber;
            
            // Only increment counters if this is a new participant
            if (index === null) {
                participantCount++;
                nextParticipantNumber++;
            }
            
            const participantHtml = `
                <div class="participant-section mb-4" id="participant-${partIndex}">
                    <div class="participant-header">
                        <h5 class="participant-title">Participant #${partIndex}</h5>
                        <span class="remove-participant text-danger" style="cursor: pointer;" data-participant="${participantCount}">
                            <i class="fas fa-times"></i> Remove
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="participants[${participantCount}][firstname]" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="participants[${participantCount}][lastname]" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>WhatsApp Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+91</span>
                                    </div>
                                    <input type="text" class="form-control" name="participants[${participantCount}][whatsapp_number]" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Age <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="participants[${participantCount}][age]" min="1" max="120" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gender <span class="text-danger">*</span></label>
                                <select class="form-control" name="participants[${participantCount}][gender]" required>
                                    <option value="">-- Select Gender --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="participants[${participantCount}][email]" required>
                            </div>
                        </div>
                    </div>
                </div>
                ${participantCount < maxParticipants ? `
                <div class="text-center mb-4">
                    <button type="button" class="btn btn-outline-primary add-another-participant">
                        <i class="fas fa-plus"></i> Add Another Participant
                    </button>
                </div>
                ` : ''}
            `;
            
            // Handle adding the participant section
            if (currentCount === 0) {
                $('#participants-container').html(participantHtml);
            } else {
                $('#add-another-participant').parent().remove();
                $('#participants-container').append(participantHtml);
            }
            
            // No need to increment participantCount here as it's already incremented at the start
            // Just update the hidden field with the current count
            $('#additional_participants').val(participantCount);
            
            // Disable add buttons if we've reached the maximum
            if ($('.participant-section').length >= maxParticipants) {
                $('#add-participant, .add-another-participant').prop('disabled', true);
            }
        }
        
        // Handle add another participant
        $(document).on('click', '.add-another-participant', function() {
            addParticipant();
        });
        
        // Original add participant handler (kept for compatibility)
        $('#add-participant').on('click', function(e) {
            e.preventDefault();
            
            if (participantCount >= maxParticipants) {
                alert('Maximum of ' + maxParticipants + ' additional participants allowed.');
                return;
            }
            
            participantCount++;
            $('#additional_participants').val(participantCount);
            
            const participantHtml = `
                <div class="participant-card card mb-3" id="participant-${participantCount}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Participant #${participantCount}</h5>
                        <button type="button" class="btn btn-sm btn-danger remove-participant" data-participant="${participantCount}">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="participants[${participantCount}][firstname]" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="participants[${participantCount}][lastname]" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>WhatsApp Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+91</span>
                                    </div>
                                    <input type="text" class="form-control" name="participants[${participantCount}][whatsapp_number]" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Age <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="participants[${participantCount}][age]" min="1" max="120" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gender <span class="text-danger">*</span></label>
                                <select class="form-control" name="participants[${participantCount}][gender]" required>
                                    <option value="">-- Select Gender --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="participants[${participantCount}][email]" required>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#participants-container').append(participantHtml);
            
            // Update add button state
            if (participantCount >= maxParticipants) {
                $('#add-participant').prop('disabled', true);
            }
        });
        
        // Remove participant
        $(document).on('click', '.remove-participant', function() {
            const participantDiv = $(this).closest('.participant-section');
            const participantId = participantDiv.attr('id').replace('participant-', '');
            
            // Remove the participant section and the add button after it
            participantDiv.next('.text-center').remove();
            participantDiv.remove();
            
            // Update the count based on actual DOM elements
            const currentCount = $('.participant-section').length;
            participantCount = currentCount;
            $('#additional_participants').val(participantCount);
            
            // Update the numbering of remaining participants
            $('.participant-section').each(function(index) {
                const newNumber = index + 1;
                $(this).attr('id', 'participant-' + newNumber);
                $(this).find('.participant-title').text('Participant #' + newNumber);
                
                // Update the data attributes and input names
                $(this).find('[name^="participants["]').each(function() {
                    const name = $(this).attr('name').replace(/participants\[\d+\]/, 'participants[' + newNumber + ']');
                    $(this).attr('name', name);
                });
            });
            
            // Update nextParticipantNumber
            nextParticipantNumber = currentCount + 1;
            
            // Re-enable add buttons if we're below max
            if (currentCount < maxParticipants) {
                $('#add-participant, .add-another-participant').prop('disabled', false);
            }
            
            // If we removed the last "Add Another" button, add it back if we're below max
            if (currentCount < maxParticipants && $('.add-another-participant').length === 0) {
                const addButtonHtml = `
                    <div class="text-center mb-4">
                        <button type="button" class="btn btn-outline-primary add-another-participant">
                            <i class="fas fa-plus"></i> Add Another Participant
                        </button>
                    </div>`;
                $('#participants-container').append(addButtonHtml);
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
                // Check for validation warnings
                const warnings = [];
                
                // Check if retreat is selected
                const retreatId = $('#retreat_id').val();
                if (!retreatId) {
                    warnings.push('Please select a retreat');
                }
                
                // Check if primary participant meets criteria
                const gender = $('#gender').val();
                const age = parseInt($('#age').val());
                const congregation = $('#congregation').val();
                const criteria = $('#retreat_id option:selected').data('criteria');
                
                if (criteria) {
                    let meetsCriteria = false;
                    
                    switch (criteria) {
                        case 'male_only':
                            meetsCriteria = gender === 'male';
                            break;
                        case 'female_only':
                            meetsCriteria = gender === 'female';
                            break;
                        case 'priests_only':
                            meetsCriteria = congregation !== '';
                            break;
                        case 'sisters_only':
                            meetsCriteria = gender === 'female' && congregation !== '';
                            break;
                        case 'youth_only':
                            meetsCriteria = age >= 18 && age <= 35;
                            break;
                        case 'children':
                            meetsCriteria = age < 18;
                            break;
                        case 'no_criteria':
                        default:
                            meetsCriteria = true;
                    }
                    
                    if (!meetsCriteria) {
                        warnings.push('Primary participant does not meet the retreat criteria');
                    }
                }
                
                // Check for past bookings
                const whatsappNumber = $('#whatsapp_number').val();
                const fullName = $('#firstname').val() + ' ' + $('#lastname').val();
                
                if (whatsappNumber && fullName.trim()) {
                    // This would be an AJAX call in a real implementation
                    // For now, we'll just show a warning if the fields are filled
                    warnings.push('Note: System will check for past bookings during submission');
                }
                
                // Show warnings if any
                if (warnings.length > 0) {
                    const $validationMessages = $('#validation-messages');
                    const $validationList = $('#validation-errors');
                    
                    $validationList.empty();
                    
                    warnings.forEach(function(warning) {
                        $validationList.append(`<li>${warning}</li>`);
                    });
                    
                    $validationMessages.removeClass('d-none');
                    
                    // Scroll to validation messages
                    $('html, body').animate({
                        scrollTop: $validationMessages.offset().top - 100
                    }, 500);
                    
                    // Still allow form submission with warnings
                    form.submit();
                } else {
                    form.submit();
                }
            }
        });
    });
</script>
@endpush
