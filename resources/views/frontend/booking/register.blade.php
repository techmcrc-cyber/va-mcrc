@extends('frontend.layout')

@section('title', 'Register for Retreat - My Retreat Booking')

@push('styles')
<style>
    .participant-card {
        border-left: 4px solid var(--primary-color);
    }
    .form-label.required::after {
        content: " *";
        color: red;
    }
</style>
@endpush

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Register for Retreat</h1>

    @if($errors->any())
    <div class="alert alert-danger">
        <strong>Please correct the following errors:</strong>
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
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Select Retreat</h5>
                <div class="mb-3">
                    <label for="retreat_id" class="form-label required">Choose Retreat</label>
                    <select name="retreat_id" id="retreat_id" class="form-select" required>
                        <option value="">-- Select a Retreat --</option>
                        @foreach($retreats as $r)
                            <option value="{{ $r->id }}" 
                                    {{ (old('retreat_id', $retreat?->id) == $r->id) ? 'selected' : '' }}
                                    data-criteria="{{ $r->criteria }}">
                                {{ $r->title }} ({{ $r->start_date->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="retreatInfo" class="alert alert-info" style="display: none;"></div>
            </div>
        </div>

        <!-- Participants -->
        <div id="participantsContainer">
            <!-- Participant 1 (Primary) -->
            <div class="card mb-4 participant-card" data-participant="1">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Participant 1 (Primary Contact)</h5>
                </div>
                <div class="card-body">
                    @include('frontend.booking.participant-form', ['index' => 0, 'isPrimary' => true])
                </div>
            </div>
        </div>

        <!-- Add Participant Button -->
        <div class="mb-4">
            <button type="button" class="btn btn-outline-primary" id="addParticipant">
                <i class="fas fa-plus"></i> Add Another Participant (Max 4)
            </button>
            <small class="text-muted d-block mt-2">You can register up to 4 participants in one booking</small>
        </div>

        <!-- Submit -->
        <div class="card">
            <div class="card-body">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                    <label class="form-check-label" for="agreeTerms">
                        I agree that the information provided is accurate and I understand the retreat guidelines
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-check"></i> Complete Registration
                </button>
                <a href="{{ route('retreats.index') }}" class="btn btn-outline-secondary btn-lg ms-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let participantCount = 1;
const maxParticipants = 4;

document.getElementById('addParticipant').addEventListener('click', function() {
    if (participantCount >= maxParticipants) {
        alert('Maximum 4 participants allowed');
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
        <div class="card mb-4 participant-card" data-participant="${num}">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Participant ${num}</h5>
                <button type="button" class="btn btn-sm btn-danger remove-participant">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            <div class="card-body">
                ${getParticipantFormHTML(index, false)}
            </div>
        </div>
    `;
}

function getParticipantFormHTML(index, isPrimary) {
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
            <div class="col-md-6 mb-3">
                <label class="form-label required">WhatsApp Number</label>
                <input type="text" name="participants[${index}][whatsapp_number]" class="form-control" maxlength="10" pattern="[0-9]{10}" required>
                <small class="text-muted">10 digits only</small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label required">Age</label>
                <input type="number" name="participants[${index}][age]" class="form-control" min="1" max="120" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label required">Email</label>
                <input type="email" name="participants[${index}][email]" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
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
            <div class="col-md-6 mb-3">
                <label class="form-label required">City</label>
                <input type="text" name="participants[${index}][city]" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label required">State</label>
                <input type="text" name="participants[${index}][state]" class="form-control" required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label required">Address</label>
                <textarea name="participants[${index}][address]" class="form-control" rows="2" required></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Diocese</label>
                <input type="text" name="participants[${index}][diocese]" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Parish</label>
                <input type="text" name="participants[${index}][parish]" class="form-control">
            </div>
            <div class="col-md-12 mb-3 congregation-field" style="display: none;">
                <label class="form-label">Congregation</label>
                <input type="text" name="participants[${index}][congregation]" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label required">Emergency Contact Name</label>
                <input type="text" name="participants[${index}][emergency_contact_name]" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label required">Emergency Contact Phone</label>
                <input type="text" name="participants[${index}][emergency_contact_phone]" class="form-control" required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Special Remarks</label>
                <textarea name="participants[${index}][special_remarks]" class="form-control" rows="2"></textarea>
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
