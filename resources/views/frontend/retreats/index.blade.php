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
                    <h5 class="card-title">{{ $retreat->title }}</h5>
                    <p class="card-text text-muted">
                        <i class="fas fa-calendar"></i> 
                        {{ $retreat->start_date->format('M d, Y') }} - {{ $retreat->end_date->format('M d, Y') }}
                    </p>
                    <p class="card-text">{{ Str::limit($retreat->short_description, 120) }}</p>
                    
                    <div class="mb-3">
                        @php
                            $bookedSeats = $retreat->bookings()->active()->count();
                            $availableSeats = $retreat->seats - $bookedSeats;
                            $percentage = ($bookedSeats / $retreat->seats) * 100;
                        @endphp
                        <div class="d-flex justify-content-between mb-1">
                            <small>Availability</small>
                            <small>{{ $availableSeats }} / {{ $retreat->seats }} seats</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar {{ $percentage > 80 ? 'bg-danger' : ($percentage > 50 ? 'bg-warning' : 'bg-success') }}" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $retreat->criteria)) }}</span>
                        <a href="{{ route('retreats.show', $retreat->id) }}" class="btn btn-sm btn-primary">
                            View Details <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $retreats->links() }}
    </div>
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> No upcoming retreats available at the moment. Please check back later.
    </div>
    @endif
</div>
@endsection
