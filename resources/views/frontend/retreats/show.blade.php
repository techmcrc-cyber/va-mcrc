@extends('frontend.layout')

@section('title', $retreat->title . ' - My Retreat Booking')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-3">{{ $retreat->title }}</h1>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">About This Retreat</h5>
                    <p class="card-text">{{ $retreat->description }}</p>
                </div>
            </div>

            @if($retreat->instructions)
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> Instructions</h5>
                    <p class="card-text">{{ $retreat->instructions }}</p>
                </div>
            </div>
            @endif

            @if($retreat->special_remarks)
            <div class="alert alert-warning">
                <strong><i class="fas fa-exclamation-triangle"></i> Special Remarks:</strong>
                <p class="mb-0 mt-2">{{ $retreat->special_remarks }}</p>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title">Retreat Details</h5>
                    
                    <div class="mb-3">
                        <strong><i class="fas fa-calendar-alt"></i> Dates:</strong>
                        <p class="mb-1">{{ $retreat->start_date->format('M d, Y') }}</p>
                        <p class="mb-0">{{ $retreat->end_date->format('M d, Y') }}</p>
                        <small class="text-muted">{{ $retreat->start_date->diffInDays($retreat->end_date) + 1 }} days</small>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-clock"></i> Timings:</strong>
                        <p class="mb-0">{{ $retreat->timings }}</p>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-users"></i> Availability:</strong>
                        <p class="mb-1">{{ $availableSeats }} of {{ $retreat->seats }} seats available</p>
                        @php
                            $percentage = (($retreat->seats - $availableSeats) / $retreat->seats) * 100;
                        @endphp
                        <div class="progress">
                            <div class="progress-bar {{ $percentage > 80 ? 'bg-danger' : ($percentage > 50 ? 'bg-warning' : 'bg-success') }}" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-filter"></i> Criteria:</strong>
                        <p class="mb-0">
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $retreat->criteria)) }}</span>
                        </p>
                    </div>

                    <hr>

                    @if($availableSeats > 0)
                        <a href="{{ route('booking.register', ['retreat_id' => $retreat->id]) }}" class="btn btn-primary w-100 btn-lg">
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
