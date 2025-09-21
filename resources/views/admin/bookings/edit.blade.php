@extends('admin.layouts.app')

@section('title', 'Edit Booking: ' . $booking->booking_id)

@push('styles')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<!-- Datepicker -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
<style>
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
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Retreat Information</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="retreat_id">Select Retreat <span class="text-danger">*</span></label>
                                    <select name="retreat_id" id="retreat_id" class="form-control select2" required>
                                        <option value="">-- Select Retreat --</option>
                                        @foreach($retreats as $retreat)
                                            <option value="{{ $retreat->id }}" 
                                                {{ $retreat->id == $booking->retreat_id ? 'selected' : '' }}
                                                data-criteria="{{ $retreat->criteria }}"
                                                data-start-date="{{ $retreat->start_date->format('Y-m-d') }}"
                                                data-end-date="{{ $retreat->end_date->format('Y-m-d') }}">
                                                {{ $retreat->title }} ({{ $retreat->start_date->format('M d, Y') }} - {{ $retreat->end_date->format('M d, Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted" id="retreat-criteria">
                                        @php
                                            $criteriaMap = [
                                                'male_only' => 'Only for Males',
                                                'female_only' => 'Only for Females',
                                                'priests_only' => 'Only for Priests',
                                                'sisters_only' => 'Only for Sisters',
                                                'youth_only' => 'Only for Youth (18-35 years)',
                                                'children' => 'Only for Children (below 18 years)',
                                                'no_criteria' => 'Open to All'
                                            ];
                                            $currentRetreat = $retreats->firstWhere('id', $booking->retreat_id);
                                            echo 'Criteria: ' . ($currentRetreat ? ($criteriaMap[$currentRetreat->criteria] ?? 'Not specified') : 'N/A');
                                        @endphp
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="special_remarks">Special Remarks</label>
                                    <textarea name="special_remarks" id="special_remarks" class="form-control" rows="2" placeholder="Any special instructions or remarks">{{ $booking->special_remarks }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Primary Participant -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h4>Primary Participant <small class="text-muted">(Main Contact Person)</small></h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstname">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" value="{{ $booking->firstname }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $booking->lastname }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="whatsapp_number">WhatsApp Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">+91</span>
                                        </div>
                                        <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="{{ $booking->whatsapp_number }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="age">Age <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="age" name="age" min="1" max="120" value="{{ $booking->age }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="male" {{ $booking->gender == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $booking->gender == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ $booking->gender == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ $booking->email }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address" name="address" value="{{ $booking->address }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ $booking->city }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">State <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="state" name="state" value="{{ $booking->state }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="diocese">Diocese</label>
                                    <input type="text" class="form-control" id="diocese" name="diocese" value="{{ $booking->diocese }}">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="parish">Parish</label>
                                    <input type="text" class="form-control" id="parish" name="parish" value="{{ $booking->parish }}">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="congregation">Congregation (For Priests/Sisters)</label>
                                    <input type="text" class="form-control" id="congregation" name="congregation" value="{{ $booking->congregation }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_name">Emergency Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ $booking->emergency_contact_name }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_phone">Emergency Contact Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ $booking->emergency_contact_phone }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Participants -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4>Additional Participants <small class="text-muted">(Maximum 3)</small></h4>
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
                                            <div class="participant-section" id="participant-{{ $participantCount }}">
                                                <div class="participant-header">
                                                    <h5 class="participant-title">Participant #{{ $participantCount }}</h5>
                                                    <span class="remove-participant" data-participant="{{ $participantCount }}">
                                                        <i class="fas fa-times"></i> Remove
                                                    </span>
                                                </div>
                                                <input type="hidden" name="participants[{{ $participantCount }}][id]" value="{{ $participant->id }}">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>First Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="participants[{{ $participantCount }}][firstname]" value="{{ $participant->firstname }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Last Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="participants[{{ $participantCount }}][lastname]" value="{{ $participant->lastname }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>WhatsApp Number <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">+91</span>
                                                                </div>
                                                                <input type="text" class="form-control" name="participants[{{ $participantCount }}][whatsapp_number]" value="{{ $participant->whatsapp_number }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Age <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" name="participants[{{ $participantCount }}][age]" min="1" max="120" value="{{ $participant->age }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Gender <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="participants[{{ $participantCount }}][gender]" required>
                                                                <option value="">-- Select Gender --</option>
                                                                <option value="male" {{ $participant->gender == 'male' ? 'selected' : '' }}>Male</option>
                                                                <option value="female" {{ $participant->gender == 'female' ? 'selected' : '' }}>Female</option>
                                                                <option value="other" {{ $participant->gender == 'other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Email Address <span class="text-danger">*</span></label>
                                                            <input type="email" class="form-control" name="participants[{{ $participantCount }}][email]" value="{{ $participant->email }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @php $participantCount++; @endphp
                                        @endif
                                    @endforeach
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
            theme: 'bootstrap4'
        });
        
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
                'youth_only': 'Only for Youth (18-35 years)',
                'children': 'Only for Children (below 18 years)',
                'no_criteria': 'Open to All'
            };
            
            $('#retreat-criteria').text('Criteria: ' + (criteriaMap[criteria] || 'Not specified'));
        });
        
        // Add participant
        let participantCount = {{ $allParticipants->count() - 1 }};
        const maxParticipants = 3;
        
        $('#add-participant').on('click', function(e) {
            e.preventDefault();
            
            if (participantCount >= maxParticipants) {
                alert('Maximum of ' + maxParticipants + ' additional participants allowed.');
                return;
            }
            
            participantCount++;
            $('#additional_participants').val(participantCount);
            
            const participantHtml = `
                <div class="participant-section" id="participant-${participantCount}">
                    <div class="participant-header">
                        <h5 class="participant-title">Participant #${participantCount}</h5>
                        <span class="remove-participant" data-participant="${participantCount}">
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
            `;
            
            $('#participants-container').append(participantHtml);
            
            // Update add button state
            if (participantCount >= maxParticipants) {
                $('#add-participant').prop('disabled', true);
            }
        });
        
        // Remove participant
        $(document).on('click', '.remove-participant', function() {
            if (confirm('Are you sure you want to remove this participant? This action cannot be undone.')) {
                const participantNum = $(this).data('participant');
                $(`#participant-${participantNum}`).remove();
                
                // Rename remaining participants
                let currentCount = 0;
                $('.participant-section').each(function(index) {
                    currentCount++;
                    const $section = $(this);
                    const newId = `participant-${currentCount}`;
                    
                    // Update IDs and data attributes
                    $section.attr('id', newId);
                    $section.find('.participant-title').text(`Participant #${currentCount}`);
                    $section.find('.remove-participant').data('participant', currentCount);
                    
                    // Update input names
                    $section.find('input, select').each(function() {
                        const name = $(this).attr('name').replace(/\[\d+\]/, `[${currentCount}]`);
                        $(this).attr('name', name);
                    });
                });
                
                participantCount = currentCount;
                $('#additional_participants').val(participantCount);
                
                // Re-enable add button if needed
                if (participantCount < maxParticipants) {
                    $('#add-participant').prop('disabled', false);
                }
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
