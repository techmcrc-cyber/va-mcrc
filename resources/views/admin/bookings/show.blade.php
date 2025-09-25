@extends('admin.layouts.app')

@section('title', 'View Booking: ' . $booking->booking_id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Booking Details: {{ $booking->booking_id }}</h3>
                    <div>
                        <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @if($booking->flag)
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Flagged Booking</h5>
                            <p>This booking has been flagged with: <strong>{{ $booking->flag }}</strong></p>
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Retreat Information</h4>
                            <hr>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%;">Retreat</th>
                                    <td>{{ $booking->retreat->title }}</td>
                                </tr>
                                <tr>
                                    <th>Dates</th>
                                    <td>
                                        {{ $booking->retreat->start_date->format('F j, Y') }} to 
                                        {{ $booking->retreat->end_date->format('F j, Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Timings</th>
                                    <td>{{ $booking->retreat->timings }}</td>
                                </tr>
                                <tr>
                                    <th>Criteria</th>
                                    <td>
                                        @php
                                            $criteriaMap = [
                                                'male_only' => 'Only for Males',
                                                'female_only' => 'Only for Females',
                                                'priests_only' => 'Only for Priests',
                                                'sisters_only' => 'Only for Sisters',
                                                'youth_only' => 'Only for Youth (18-35 years)',
                                                'children' => 'Only for Children (below 18 years)',
                                                'no_criteria' => 'Open to All'
                                            ];
                                            echo $criteriaMap[$booking->retreat->criteria] ?? 'Not specified';
                                        @endphp
                                    </td>
                                </tr>
                                <tr>
                                    <th>Special Remarks</th>
                                    <td>{{ $booking->special_remarks ?? 'None' }}</td>
                                </tr>
                            </table>
                            
                            <h4 class="mt-4">Primary Participant Details</h4>
                            <hr>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%;">Name</th>
                                    <td>{{ $booking->firstname }} {{ $booking->lastname }}</td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>{{ ucfirst($booking->gender) }}</td>
                                </tr>
                                <tr>
                                    <th>Age</th>
                                    <td>{{ $booking->age }} years</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $booking->email }}</td>
                                </tr>
                                <tr>
                                    <th>WhatsApp</th>
                                    <td>+91 {{ $booking->whatsapp_number }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $booking->address }}, {{ $booking->city }}, {{ $booking->state }}</td>
                                </tr>
                                @if($booking->diocese)
                                    <tr>
                                        <th>Diocese</th>
                                        <td>{{ $booking->diocese }}</td>
                                    </tr>
                                @endif
                                @if($booking->parish)
                                    <tr>
                                        <th>Parish</th>
                                        <td>{{ $booking->parish }}</td>
                                    </tr>
                                @endif
                                @if($booking->congregation)
                                    <tr>
                                        <th>Congregation</th>
                                        <td>{{ $booking->congregation }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Booking Information</h4>
                            <hr>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%;">Booking ID</th>
                                    <td>{{ $booking->booking_id }}</td>
                                </tr>
                                <tr>
                                    <th>Booking Date</th>
                                    <td>{{ $booking->created_at->format('F j, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Total Participants</th>
                                    <td>{{ $booking->additional_participants + 1 }}</td>
                                </tr>
                                <tr>
                                    <th>Booked By</th>
                                    <td>{{ $booking->creator->name ?? 'System' }}</td>
                                </tr>
                                @if($booking->flag)
                                    <tr class="table-warning">
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-warning">
                                                {{ str_replace('_', ' ', $booking->flag) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                            
                            <h4 class="mt-4">Emergency Contact</h4>
                            <hr>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%;">Name</th>
                                    <td>{{ $booking->emergency_contact_name }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $booking->emergency_contact_phone }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($allParticipants->count() > 1)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Additional Participants ({{ $allParticipants->count() - 1 }})</h4>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Gender</th>
                                                <th>Age</th>
                                                <th>Email</th>
                                                <th>WhatsApp</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($allParticipants as $index => $participant)
                                                @if($participant->id !== $booking->id)
                                                    <tr>
                                                        <td>{{ $index }}</td>
                                                        <td>{{ $participant->firstname }} {{ $participant->lastname }}</td>
                                                        <td>{{ ucfirst($participant->gender) }}</td>
                                                        <td>{{ $participant->age }} years</td>
                                                        <td>{{ $participant->email }}</td>
                                                        <td>+91 {{ $participant->whatsapp_number }}</td>
                                                        <td>
                                                            @if($participant->flag)
                                                                <span class="badge bg-warning" data-toggle="tooltip" title="{{ $participant->flag }}">
                                                                    <i class="fas fa-exclamation-triangle"></i>
                                                                    {{ Str::title(str_replace('_', ' ', explode(',', $participant->flag)[0])) }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-success">Confirmed</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <form action="{{ route('admin.bookings.cancel-participant', $participant->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this participant?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel Participant">
                                                                    <i class="fas fa-user-times"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Booking Actions</h3>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-primary me-2">
                                            <i class="fas fa-edit"></i> Edit Booking
                                        </a>
                                        <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this entire booking? This will deactivate all participants in this booking.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-ban"></i> Cancel Booking
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                Created: {{ $booking->created_at->format('M j, Y h:i A') }} 
                                by {{ $booking->creator->name ?? 'System' }}
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">
                                Last Updated: {{ $booking->updated_at->format('M j, Y h:i A') }}
                                @if($booking->updater)
                                    by {{ $booking->updater->name }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Print styles
        $('.btn-print').on('click', function() {
            window.print();
            return false;
        });
    });
</script>
@endpush
