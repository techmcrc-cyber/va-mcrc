@extends('frontend.layout')

@section('title', $retreat['retreat_name'] . ' - My Retreat Booking')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-3">{{ $retreat['retreat_name'] }}</h1>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">About This Retreat</h5>
                    <p class="card-text">{{ $retreat['description'] }}</p>
                </div>
            </div>

            @if(!empty($retreat['details']['instructions']))
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> Instructions</h5>
                    <p class="card-text">{{ $retreat['details']['instructions'] }}</p>
                </div>
            </div>
            @endif

            @if(!empty($retreat['details']['special_remarks']))
            <div class="alert alert-warning">
                <strong><i class="fas fa-exclamation-triangle"></i> Special Remarks:</strong>
                <p class="mb-0 mt-2">{{ $retreat['details']['special_remarks'] }}</p>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title">Retreat Details</h5>
                    
                    <div class="mb-3">
                        <strong><i class="fas fa-calendar-alt"></i> Dates:</strong>
                        <p class="mb-1">{{ \Carbon\Carbon::parse($retreat['start_date'])->format('M d, Y') }}</p>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($retreat['end_date'])->format('M d, Y') }}</p>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($retreat['start_date'])->diffInDays(\Carbon\Carbon::parse($retreat['end_date'])) + 1 }} days</small>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-clock"></i> Timings:</strong>
                        <p class="mb-0">{{ $retreat['timings'] }}</p>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-users"></i> Availability:</strong>
                        <p class="mb-1">{{ $retreat['availability']['available_spots'] }} of {{ $retreat['availability']['total_seats'] }} seats available</p>
                        @php
                            $totalSeats = $retreat['availability']['total_seats'];
                            $bookedSeats = $retreat['availability']['booked_seats'];
                            $percentage = $totalSeats > 0 ? ($bookedSeats / $totalSeats) * 100 : 0;
                        @endphp
                        <div class="progress">
                            <div class="progress-bar {{ $percentage > 80 ? 'bg-danger' : ($percentage > 50 ? 'bg-warning' : 'bg-success') }}" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-filter"></i> Criteria:</strong>
                        <p class="mb-0">
                            <span class="badge bg-secondary">{{ $retreat['criteria']['label'] }}</span>
                        </p>
                    </div>

                    <hr>

                    @if($retreat['availability']['is_available'])
                        <a href="{{ route('booking.register', ['retreat_id' => $retreat['retreat_id']]) }}" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-user-plus"></i> Register Now
                        </a>
                    @else
                        <button class="btn btn-secondary w-100 btn-lg" disabled>
                            <i class="fas fa-times-circle"></i> Fully Booked
                        </button>
                    @endif

                    <a href="{{ route('retreats.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="fas fa-arrow-left"></i> Back to Retreats
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
