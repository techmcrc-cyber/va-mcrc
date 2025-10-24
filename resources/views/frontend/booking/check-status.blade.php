@extends('frontend.layout')

@section('title', 'Check Booking Status - My Retreat Booking')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-search fa-3x text-primary mb-3"></i>
                        <h2>Check Booking Status</h2>
                        <p class="text-muted">Enter your booking details to view your retreat information</p>
                    </div>

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
                            <label for="booking_id" class="form-label">Booking ID</label>
                            <input type="text" 
                                   name="booking_id" 
                                   id="booking_id" 
                                   class="form-control form-control-lg" 
                                   placeholder="e.g., BK20241024001"
                                   value="{{ old('booking_id') }}"
                                   required>
                            <small class="text-muted">The booking ID sent to your email</small>
                        </div>

                        <div class="mb-4">
                            <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                            <input type="text" 
                                   name="whatsapp_number" 
                                   id="whatsapp_number" 
                                   class="form-control form-control-lg" 
                                   placeholder="10 digit number"
                                   maxlength="10"
                                   pattern="[0-9]{10}"
                                   value="{{ old('whatsapp_number') }}"
                                   required>
                            <small class="text-muted">The WhatsApp number used during registration</small>
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

            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-info-circle"></i> Need Help?</h6>
                    <p class="card-text small mb-0">
                        If you're having trouble finding your booking, please contact us at 
                        <a href="mailto:support@myretreatbooking.com">support@myretreatbooking.com</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
