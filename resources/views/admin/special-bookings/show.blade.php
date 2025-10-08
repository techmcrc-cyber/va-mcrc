@extends('admin.layouts.app')

@section('title', 'Special Booking Details')

@section('content')
<div class="container-fluid">
    <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="m-0 fw-bold">Special Booking: {{ $specialBooking->booking_id }}</h4>
            <a href="{{ route('admin.special-bookings.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    @if($specialBooking->flag)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Validation Flags:</strong> 
        @foreach(explode(',', $specialBooking->flag) as $flag)
            <span class="badge bg-warning text-dark">{{ $flag }}</span>
        @endforeach
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Retreat Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Retreat</th>
                            <td>{{ $specialBooking->retreat->title }}</td>
                        </tr>
                        <tr>
                            <th>Dates</th>
                            <td>{{ $specialBooking->retreat->start_date->format('M d, Y') }} - {{ $specialBooking->retreat->end_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Criteria</th>
                            <td>{{ $specialBooking->retreat->criteriaRelation ? $specialBooking->retreat->criteriaRelation->name : 'No Criteria' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Booking Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Booking ID</th>
                            <td>{{ $specialBooking->booking_id }}</td>
                        </tr>
                        <tr>
                            <th>Created Date</th>
                            <td>{{ $specialBooking->created_at->format('M d, Y H:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Total Participants</th>
                            <td>{{ $allParticipants->count() }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title">Participants</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Contact</th>
                            <th>Flags</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allParticipants as $participant)
                        <tr>
                            <td>{{ $participant->participant_number }}</td>
                            <td>
                                {{ $participant->firstname }} {{ $participant->lastname }}
                                @if($participant->participant_number === 1)
                                    <span class="badge bg-primary">Primary</span>
                                @endif
                            </td>
                            <td>{{ $participant->age }}</td>
                            <td>{{ ucfirst($participant->gender) }}</td>
                            <td>
                                {{ $participant->email }}<br>
                                <small>{{ $participant->whatsapp_number }}</small>
                            </td>
                            <td>
                                @if($participant->flag)
                                    @foreach(explode(',', $participant->flag) as $flag)
                                        <span class="badge bg-warning text-dark">{{ $flag }}</span><br>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
