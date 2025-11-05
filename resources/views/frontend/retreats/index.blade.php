@extends('frontend.layout')

@section('title', 'Upcoming Retreats - Mount Carmel Retreat Centre')
@section('meta_description', 'Browse and book upcoming spiritual retreats at Mount Carmel Retreat Centre. View available dates, retreat schedules, and secure your spot for a transformative spiritual experience.')
@section('meta_keywords', 'upcoming retreats, retreat schedule, book retreat, spiritual retreat dates, Catholic retreat Kerala, retreat availability, religious retreat booking, Mount Carmel retreats')
@section('og_title', 'Upcoming Retreats - Book Your Spiritual Journey')
@section('og_description', 'Explore upcoming retreat schedules at Mount Carmel Retreat Centre. Find the perfect retreat for your spiritual journey and book your spot today.')

@push('styles')
<style>
    .pagination {
        display: flex;
        gap: 0.5rem;
        margin: 0;
        padding: 0;
    }
    
    .pagination .page-item {
        list-style: none;
    }
    
    .pagination .page-item .page-link {
        border: 1px solid #dee2e6 !important;
        color: #495057 !important;
        padding: 0.5rem 0.75rem;
        border-radius: 4px !important;
        transition: all 0.3s;
        background-color: white !important;
        text-decoration: none;
        display: inline-block;
        min-width: 40px;
        text-align: center;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #ba4165 !important;
        border-color: #ba4165 !important;
        color: white !important;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d !important;
        background-color: #fff !important;
        border-color: #dee2e6 !important;
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .pagination .page-item:not(.disabled):not(.active) .page-link:hover {
        background-color: #ba4165 !important;
        border-color: #ba4165 !important;
        color: white !important;
    }
</style>
@endpush

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Upcoming Retreats</h1>

    @if($retreats->total() > 0)
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
                   <!--  <p class="card-text text-muted mb-2">
                        <i class="fas fa-clock"></i> 
                        {{ $retreat['timings'] ?? 'Check details' }}
                    </p> -->
                    <p class="card-text mb-3">
                        <i class="fas fa-filter"></i> 
                        <strong>Eligibility:</strong> {{ $retreat['criteria_name'] ?? 'Open to all' }}
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

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary" style="padding: 0.25rem 0.5rem;">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('retreats.show', $retreat['retreat_id']) }}" class="btn btn-sm {{ $retreat['available_spots'] > 0 ? 'btn-primary' : 'btn-secondary' }}" style="min-width: 140px; padding: 0.25rem 0.5rem;">
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

    @if($retreats->total() > 0 && $retreats->hasPages())
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($retreats->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">&laquo; Previous</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $retreats->previousPageUrl() }}" rel="prev">&laquo; Previous</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($retreats->getUrlRange(1, $retreats->lastPage()) as $page => $url)
                    @if ($page == $retreats->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($retreats->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $retreats->nextPageUrl() }}" rel="next">Next &raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Next &raquo;</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
</div>
@endsection
