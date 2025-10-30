@extends('frontend.layout')

@section('title', 'Register for Retreat - Mount Carmel Retreat Centre')
@section('meta_description', 'Complete your retreat registration at Mount Carmel Retreat Centre. Fill out the booking form to secure your spot for an upcoming spiritual retreat in Kerala.')
@section('meta_keywords', 'retreat registration, book retreat, retreat booking form, spiritual retreat registration, Catholic retreat booking, Mount Carmel registration, Kerala retreat booking')
@section('og_title', 'Register for Your Spiritual Retreat')
@section('og_description', 'Secure your spot at Mount Carmel Retreat Centre. Complete the registration form and begin your journey of faith renewal and spiritual growth.')

@push('styles')
<style>
    .registration-header {
        background: white;
        color: var(--text-dark);
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-bottom: 1px solid #e8e8e8;
    }
    
    .registration-header h1 {
        color: var(--text-dark);
    }
    
    .registration-header p {
        color: var(--text-light);
    }
    
    .participant-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .participant-card .card-header {
        background: #f8f9fa;
        color: var(--text-dark);
        padding: 1rem 1.5rem;
        border: none;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .participant-card .card-body {
        padding: 1.5rem;
        background-color: white;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .form-label.required::after {
        content: " *";
        color: #dc3545;
        font-weight: bold;
    }
    
    .form-control, .form-select {
        border: 1px solid #ddd;
        padding: 0.75rem;
        border-radius: 6px;
        transition: all 0.3s;
        font-size: 0.95rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(186, 65, 101, 0.15);
    }
    
    .btn-add-participant {
        background-color: white;
        color: var(--primary-color);
        border: 2px dashed var(--primary-color);
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        transition: all 0.3s;
    }
    
    .btn-add-participant:hover {
        background: var(--gradient-primary);
        color: white;
        border-color: transparent;
    }
    
    .btn-remove {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.9rem;
    }
    
    .btn-remove:hover {
        background-color: #c82333;
    }
    
    .retreat-info-card {
        background-color: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .submit-section {
        background-color: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="registration-header">
    <div class="container">
        <h1 class="mb-2" style="font-size: 2rem;">Register for Retreat</h1>
        <p class="mb-0">Complete the form below to secure your spot</p>
    </div>
</div>

<div class="container mb-5">

    @if($errors->any())
    <div class="alert alert-danger" style="border-radius: 8px; border-left: 4px solid #dc3545;">
        <strong><i class="fas fa-exclamation-circle"></i> Please correct the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('booking.store') }}" method="POST" id="registrationForm">
        @csrf

        <!-- Retreat Selection -->
        <div class="card mb-4" style="border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 2rem;">
                <h5 class="mb-3" style="color: var(--primary-color); font-size: 1.3rem;">
                    <i class="fas fa-calendar-alt me-2"></i>Select Retreat
                </h5>
                <div class="mb-3">
                    <label for="retreat_id" class="form-label required">Choose Your Retreat</label>
                    <select name="retreat_id" id="retreat_id" class="form-select" required {{ $retreat ? 'disabled' : '' }}>
                        <option value="">-- Select a Retreat --</option>
                        @foreach($retreats as $r)
                            <option value="{{ $r->id }}" 
                                    {{ (old('retreat_id', $retreat?->id) == $r->id) ? 'selected' : '' }}
                                    data-criteria="{{ $r->criteria }}">
                                {{ $r->title }} ({{ $r->start_date->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                    @if($retreat)
                        <input type="hidden" name="retreat_id" value="{{ $retreat->id }}">
                        <small class="text-muted mt-1 d-block"><i class="fas fa-lock"></i> Retreat pre-selected from your previous selection</small>
                    @else
                        <small class="text-muted mt-1 d-block">Select the retreat you wish to attend</small>
                    @endif
                </div>

                @if($retreats->count() > 0)
                <!-- Available Retreats List -->
                <div class="mt-4">
                    <h6 class="mb-3" style="color: var(--text-dark); font-size: 1rem;">
                        <i class="fas fa-list me-2"></i>Available Retreats
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover" style="font-size: 0.9rem;">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>Retreat Name</th>
                                    <th>Dates</th>
                                    <th>Duration</th>
                                    <th>Seats Available</th>
                                    <th>Criteria</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($retreats as $r)
                                <tr style="cursor: pointer;" onclick="document.getElementById('retreat_id').value='{{ $r->id }}'; document.getElementById('retreat_id').dispatchEvent(new Event('change'));">
                                    <td><strong>{{ $r->title }}</strong></td>
                                    <td>
                                        {{ $r->start_date->format('M d') }} - {{ $r->end_date->format('M d, Y') }}
                                    </td>
                                    <td>{{ $r->start_date->diffInDays($r->end_date) + 1 }} days</td>
                                    <td>
                                        @php
                                            $booked = $r->bookings()->where('is_active', true)->count();
                                            $available = $r->seats - $booked;
                                        @endphp
                                        <span class="badge {{ $available > 5 ? 'bg-success' : ($available > 0 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $available }} / {{ $r->seats }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $r->criteriaRelation ? $r->criteriaRelation->name : 'Open to all' }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0 mt-2">
                        <i class="fas fa-info-circle me-1"></i>Click on any row to select that retreat
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Participants -->
        <div id="participantsContainer">
            <!-- Participant 1 (Primary) -->
            <div class="card participant-card" data-participant="1">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>Participant 1 (Primary Contact)
                    </h5>
                </div>
                <div class="card-body">
                    @include('frontend.booking.participant-form', ['index' => 0, 'isPrimary' => true])
                </div>
            </div>
            
            @if(old('participants'))
                @foreach(old('participants') as $index => $participant)
                    @if($index > 0)
                    <div class="card participant-card" data-participant="{{ $index + 1 }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Participant {{ $index + 1 }}</h5>
                            <button type="button" class="btn btn-remove remove-participant">
                                <i class="fas fa-trash-alt me-1"></i> Remove
                            </button>
                        </div>
                        <div class="card-body">
                            @include('frontend.booking.participant-form', ['index' => $index, 'isPrimary' => false])
                        </div>
                    </div>
                    @endif
                @endforeach
            @endif
        </div>

        <!-- Add Participant Button -->
        <div class="mb-4 text-center">
            <button type="button" class="btn-add-participant" id="addParticipant">
                <i class="fas fa-plus-circle me-2"></i> Add Another Participant
            </button>
            <p class="text-muted mt-2 mb-0" style="font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> You can register up to {{ $maxParticipants }} participants in one booking.
            </p>
        </div>

        <!-- Submit Section -->
        <div class="submit-section">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="agreeTerms" required style="width: 1.2rem; height: 1.2rem;">
                <label class="form-check-label ms-2" for="agreeTerms" style="font-size: 0.95rem;">
                    I confirm that the information provided is accurate and I understand the retreat guidelines.
                </label>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-check-circle me-2"></i> Complete Registration
                </button>
                <a href="{{ route('retreats.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i> Back to Retreats
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Initialize participant count based on existing cards (for validation errors)
let participantCount = document.querySelectorAll('.participant-card').length;
const maxParticipants = {{ $maxParticipants }};

document.getElementById('addParticipant').addEventListener('click', function() {
    if (participantCount >= maxParticipants) {
        alert('Maximum ' + maxParticipants + ' participants allowed');
        return;
    }
    
    participantCount++;
    
    const container = document.getElementById('participantsContainer');
    const newCard = createParticipantCard(participantCount);
    container.insertAdjacentHTML('beforeend', newCard);
    
    if (participantCount >= maxParticipants) {
        this.disabled = true;
    }
});

// Remove participant
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-participant')) {
        e.target.closest('.participant-card').remove();
        participantCount--;
        document.getElementById('addParticipant').disabled = false;
        renumberParticipants();
    }
});

function renumberParticipants() {
    const cards = document.querySelectorAll('.participant-card');
    cards.forEach((card, index) => {
        const num = index + 1;
        card.querySelector('.card-header h5').textContent = 
            num === 1 ? 'Participant 1 (Primary Contact)' : `Participant ${num}`;
        card.setAttribute('data-participant', num);
        
        // Update field names
        card.querySelectorAll('[name^="participants["]').forEach(field => {
            const fieldName = field.name.match(/\[(\w+)\]$/)[1];
            field.name = `participants[${index}][${fieldName}]`;
        });
    });
}

function createParticipantCard(num) {
    const index = num - 1;
    return `
        <div class="card participant-card" data-participant="${num}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Participant ${num}</h5>
                <button type="button" class="btn btn-remove remove-participant">
                    <i class="fas fa-trash-alt me-1"></i> Remove
                </button>
            </div>
            <div class="card-body">
                ${getParticipantFormHTML(index, false)}
            </div>
        </div>
    `;
}

function getParticipantFormHTML(index, isPrimary) {
    // Simplified form for secondary participants
    return `
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label required">First Name</label>
                <input type="text" name="participants[${index}][firstname]" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label required">Last Name</label>
                <input type="text" name="participants[${index}][lastname]" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label required">WhatsApp Number</label>
                <input type="text" name="participants[${index}][whatsapp_number]" class="form-control" maxlength="10" pattern="[0-9]{10}" required>
                <small class="text-muted">10 digits only</small>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label required">Age</label>
                <input type="number" name="participants[${index}][age]" class="form-control" min="1" max="120" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label required">Gender</label>
                <select name="participants[${index}][gender]" class="form-select" required>
                    <option value="">Select</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Marital Status</label>
                <select name="participants[${index}][married]" class="form-select">
                    <option value="">Select</option>
                    <option value="yes">Married</option>
                    <option value="no">Unmarried</option>
                </select>
            </div>
            <div class="col-md-6 mb-3 congregation-field" style="display: none;">
                <label class="form-label">Congregation</label>
                <input type="text" name="participants[${index}][congregation]" class="form-control">
                <small class="text-muted">For Priests/Sisters only</small>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label required">Email Address</label>
                <input type="email" name="participants[${index}][email]" class="form-control" required>
            </div>
            <div class="col-12 mt-2">
                <p class="text-muted small mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Address and emergency contact will be copied from primary participant
                </p>
            </div>
        </div>
    `;
}

// Show/hide congregation field based on retreat criteria
document.getElementById('retreat_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const criteria = selectedOption.dataset.criteria;
    
    const congregationFields = document.querySelectorAll('.congregation-field');
    if (criteria === 'priests_only' || criteria === 'sisters_only') {
        congregationFields.forEach(field => {
            field.style.display = 'block';
            field.querySelector('input').required = true;
        });
    } else {
        congregationFields.forEach(field => {
            field.style.display = 'none';
            field.querySelector('input').required = false;
        });
    }
});
</script>
@endpush
@endsection
