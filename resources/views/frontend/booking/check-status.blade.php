@extends('frontend.layout')

@section('title', 'Check Booking Status - My Retreat Booking')

@push('styles')
<style>
    .check-status-hero {
        background: #f8f9fa;
        padding: 3rem 0 2rem;
        margin-bottom: 2rem;
    }
    
    .status-icon-wrapper {
        width: 80px;
        height: 80px;
        background: linear-gradient(136deg, #ba4165 0%, #700000 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
    
    .status-icon-wrapper i {
        font-size: 2rem;
        color: white;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(186, 65, 101, 0.15);
    }
    
    .info-card {
        background: #f8f9fa;
        border: none;
        border-radius: 8px;
    }
    
    .feature-item {
        display: flex;
        align-items: start;
        margin-bottom: 1rem;
    }
    
    .feature-icon {
        width: 40px;
        height: 40px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="check-status-hero">
    <div class="container">
        <div class="text-center">
            <div class="status-icon-wrapper">
                <i class="fas fa-search"></i>
            </div>
            <h1 class="mb-3">Check Your Booking Status</h1>
            <p class="text-muted mb-0">Enter your booking details to view your retreat information and manage your booking</p>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-4">Enter Your Details</h5>

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Error:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('booking.check-status.submit') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="booking_id" class="form-label fw-semibold">
                                <i class="fas fa-ticket-alt me-2 text-primary"></i>Booking ID
                            </label>
                            <input type="text" 
                                   name="booking_id" 
                                   id="booking_id" 
                                   class="form-control form-control-lg" 
                                   placeholder="e.g., BK20241024001"
                                   value="{{ old('booking_id') }}"
                                   required>
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Check your confirmation email for this ID</small>
                        </div>

                        <div class="mb-4">
                            <label for="whatsapp_number" class="form-label fw-semibold">
                                <i class="fab fa-whatsapp me-2 text-success"></i>WhatsApp Number
                            </label>
                            <input type="text" 
                                   name="whatsapp_number" 
                                   id="whatsapp_number" 
                                   class="form-control form-control-lg" 
                                   placeholder="10 digit number"
                                   maxlength="10"
                                   pattern="[0-9]{10}"
                                   value="{{ old('whatsapp_number') }}"
                                   required>
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Enter the number used during registration</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search"></i> Check Status
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted mb-2">Don't have a booking yet?</p>
                        <a href="{{ route('booking.register') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus"></i> Register Now
                        </a>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="col-lg-5">
            <div class="info-card p-4 mb-4">
                <h5 class="mb-3">What You Can Do</h5>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">View Details</h6>
                        <p class="text-muted small mb-0">See your complete booking information and retreat details</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Participant Info</h6>
                        <p class="text-muted small mb-0">Review all registered participants for your booking</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Retreat Schedule</h6>
                        <p class="text-muted small mb-0">Check dates, timings, and important instructions</p>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="mb-3"><i class="fas fa-question-circle text-primary"></i> Need Help?</h6>
                    <p class="small text-muted mb-3">
                        If you're having trouble finding your booking or need assistance, our support team is here to help.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="mailto:support@myretreatbooking.com" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope me-2"></i>Email Support
                        </a>
                        <a href="tel:+15551234567" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-phone me-2"></i>Call Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
