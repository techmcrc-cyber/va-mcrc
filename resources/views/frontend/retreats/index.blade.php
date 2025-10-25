@extends('frontend.layout')

@section('title', 'Browse Retreats - My Retreat Booking')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Upcoming Retreats</h1>

    @if($retreats->count() > 0)
    <div class="row g-4">
        @foreach($retreats as $retreat)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $retreat['retreat_name'] }}</h5>
                    <p class="card-text text-muted mb-2">
                        <i class="fas fa-calendar"></i> 
                        {{ \Carbon\Carbon::parse($retreat['start_date'])->format('M d, Y') }} - {{ \Carbon\Carbon::parse($retreat['end_date'])->format('M d, Y') }}
                    </p>
                    <p class="card-text text-muted mb-2">
                        <i class="fas fa-clock"></i> 
                        {{ $retreat['timings'] ?? 'Check details' }}
                    </p>
                    <p class="card-text mb-3">
                        <i class="fas fa-filter"></i> 
                        <strong>Criteria:</strong> {{ $retreat['criteria_name'] ?? 'Open to all' }}
                    </p>
                    
                    <div class="mb-3">
                        @php
                            $availableSeats = $retreat['available_spots'];
                            $totalSeats = $retreat['total_seats'];
                            $bookedSeats = $totalSeats - $availableSeats;
                            $percentage = $totalSeats > 0 ? ($bookedSeats / $totalSeats) * 100 : 0;
                        @endphp
                        <div class="d-flex justify-content-between mb-1">
                            <small>Availability</small>
                            <small>{{ $availableSeats }} / {{ $totalSeats }} seats</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar {{ $percentage > 80 ? 'bg-danger' : ($percentage > 50 ? 'bg-warning' : 'bg-success') }}" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('retreats.show', $retreat['retreat_id']) }}" class="btn btn-sm btn-primary">
                            View Details <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> No upcoming retreats available at the moment. Please check back later.
    </div>
    @endif
</div>
@endsection
