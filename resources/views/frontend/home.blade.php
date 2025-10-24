@extends('frontend.layout')

@section('title', 'Welcome - My Retreat Booking')

@section('content')
<!-- Hero Section -->
<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 mb-4">Welcome to My Retreat Booking</h1>
        <p class="lead mb-4">Find peace, renewal, and spiritual growth through our carefully curated retreats</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('retreats.index') }}" class="btn btn-light btn-lg">
                <i class="fas fa-calendar-alt"></i> Browse Retreats
            </a>
            <a href="{{ route('booking.register') }}" class="btn btn-outline-light btn-lg">
                <i class="fas fa-user-plus"></i> Register Now
            </a>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="container mb-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <i class="fas fa-search fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Find Your Retreat</h5>
                    <p class="card-text">Browse through our selection of spiritual retreats tailored to your needs</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <i class="fas fa-clipboard-check fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Easy Registration</h5>
                    <p class="card-text">Simple and secure online booking process for you and your group</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <i class="fas fa-bell fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Track Your Booking</h5>
                    <p class="card-text">Check your booking status anytime with your booking ID</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Retreats -->
@if($upcomingRetreats->count() > 0)
<div class="container mb-5">
    <h2 class="text-center mb-4">Upcoming Retreats</h2>
    <div class="row g-4">
        @foreach($upcomingRetreats as $retreat)
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $retreat['retreat_name'] }}</h5>
                    <p class="card-text text-muted">
                        <i class="fas fa-calendar"></i> 
                        {{ \Carbon\Carbon::parse($retreat['start_date'])->format('M d, Y') }} - {{ \Carbon\Carbon::parse($retreat['end_date'])->format('M d, Y') }}
                    </p>
                    <p class="card-text">{{ $retreat['criteria_label'] ?? 'Open to all' }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-info">{{ $retreat['available_spots'] }} seats left</span>
                        <a href="{{ route('retreats.show', $retreat['retreat_id']) }}" class="btn btn-sm btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="text-center mt-4">
        <a href="{{ route('retreats.index') }}" class="btn btn-outline-primary">View All Retreats</a>
    </div>
</div>
@endif

<!-- Quick Actions -->
<div class="container mb-5">
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center p-5">
                    <i class="fas fa-user-plus fa-3x mb-3"></i>
                    <h4>New Registration</h4>
                    <p>Register for an upcoming retreat</p>
                    <a href="{{ route('booking.register') }}" class="btn btn-light">Register Now</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center p-5">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h4>Check Booking Status</h4>
                    <p>View your booking details</p>
                    <a href="{{ route('booking.check-status') }}" class="btn btn-light">Check Status</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
