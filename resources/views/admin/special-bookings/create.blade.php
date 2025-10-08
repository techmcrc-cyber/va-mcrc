@extends('admin.layouts.app')

@section('title', 'Create Special Booking')

@section('content')
<div class="container-fluid">
    <div class="alert alert-info">
        <i class="fas fa-star me-2"></i>
        <strong>Super Admin Special Booking Mode:</strong> This booking form bypasses criteria validation. 
        Any criteria failures will be flagged but the booking will still be created.
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Create Special Booking</h3>
            <div class="card-tools">
                <a href="{{ route('admin.special-bookings.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Use this form to create a special booking. 
                The system will flag any criteria violations but will not prevent the booking.
            </p>
            <form action="{{ route('admin.special-bookings.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="retreat_id" class="form-label">Select Retreat <span class="text-danger">*</span></label>
                        <select class="form-select @error('retreat_id') is-invalid @enderror" 
                                id="retreat_id" name="retreat_id" required>
                            <option value="">Select a retreat</option>
                            @foreach($retreats as $retreat)
                                <option value="{{ $retreat->id }}" {{ old('retreat_id') == $retreat->id ? 'selected' : '' }}>
                                    {{ $retreat->title }} ({{ $retreat->start_date->format('M d, Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('retreat_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h5 class="mt-4 mb-3">Primary Participant Details</h5>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('firstname') is-invalid @enderror" 
                               id="firstname" name="firstname" value="{{ old('firstname') }}" required>
                        @error('firstname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('lastname') is-invalid @enderror" 
                               id="lastname" name="lastname" value="{{ old('lastname') }}" required>
                        @error('lastname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="age" class="form-label">Age <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('age') is-invalid @enderror" 
                               id="age" name="age" value="{{ old('age') }}" min="1" max="120" required>
                        @error('age')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                        <select class="form-select @error('gender') is-invalid @enderror" 
                                id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="whatsapp_number" class="form-label">WhatsApp Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('whatsapp_number') is-invalid @enderror" 
                               id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}" 
                               maxlength="10" required>
                        @error('whatsapp_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="congregation" class="form-label">Congregation (if applicable)</label>
                        <input type="text" class="form-control @error('congregation') is-invalid @enderror" 
                               id="congregation" name="congregation" value="{{ old('congregation') }}">
                        @error('congregation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                               id="city" name="city" value="{{ old('city') }}" required>
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('state') is-invalid @enderror" 
                               id="state" name="state" value="{{ old('state') }}" required>
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="emergency_contact_name" class="form-label">Emergency Contact Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                               id="emergency_contact_name" name="emergency_contact_name" 
                               value="{{ old('emergency_contact_name') }}" required>
                        @error('emergency_contact_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                               id="emergency_contact_phone" name="emergency_contact_phone" 
                               value="{{ old('emergency_contact_phone') }}" required>
                        @error('emergency_contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <input type="hidden" name="additional_participants" value="0">

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('admin.special-bookings.index') }}" class="btn btn-secondary me-md-2">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-star me-1"></i> Create Special Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
