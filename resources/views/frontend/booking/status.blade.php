@extends('frontend.layout')

@section('title', 'Booking Details - My Retreat Booking')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-ticket-alt"></i> Booking Details</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Booking ID:</strong>
                            <p class="mb-0">{{ $bookingDetails['booking_id'] }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Booking Date:</strong>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($bookingDetails['booking_date'])->format('M d, Y H:i A') }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <p class="mb-0">
                                <span class="badge bg-success">{{ ucfirst($bookingDetails['status']) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong>Retreat Status:</strong>
                            <p class="mb-0">
                                <span class="badge {{ $bookingDetails['retreat_status'] == 'upcoming' ? 'bg-info' : ($bookingDetails['retreat_status'] == 'ongoing' ? 'bg-warning' : 'bg-secondary') }}">
                                    {{ ucfirst($bookingDetails['retreat_status']) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-church"></i> Retreat Information</h5>
                </div>
                <div class="card-body">
                    <h4>{{ $bookingDetails['retreat']['name'] }}</h4>
                    <p class="text-muted">{{ $bookingDetails['retreat']['short_description'] }}</p>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar-alt"></i> Start Date:</strong>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($bookingDetails['retreat']['start_date'])->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar-check"></i> End Date:</strong>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($bookingDetails['retreat']['end_date'])->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-clock"></i> Timings:</strong>
                        <p class="mb-0">{{ $bookingDetails['retreat']['timings'] }}</p>
                    </div>

                    @if($bookingDetails['retreat']['instructions'])
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle"></i> Instructions:</strong>
                        <div class="mt-2">{!! $bookingDetails['retreat']['instructions'] !!}</div>
                    </div>
                    @endif

                    @if($bookingDetails['retreat']['special_remarks'])
                    <div class="alert alert-warning">
                        <strong><i class="fas fa-exclamation-triangle"></i> Special Remarks:</strong>
                        <div class="mt-2">{!! $bookingDetails['retreat']['special_remarks'] !!}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Participants ({{ $bookingDetails['summary']['total_participants'] }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>WhatsApp</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookingDetails['participants'] as $participant)
                                <tr>
                                    <td>{{ $participant['serial_number'] }}</td>
                                    <td>{{ $participant['full_name'] }}</td>
                                    <td>{{ $participant['email'] }}</td>
                                    <td>{{ $participant['whatsapp_number'] }}</td>
                                    <td>{{ $participant['age'] }}</td>
                                    <td>{{ ucfirst($participant['gender']) }}</td>
                                    <td>
                                        <span class="badge {{ $participant['role'] == 'primary' ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ ucfirst($participant['role']) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($bookingDetails['important_info']['is_cancellable'])
                                            <button class="btn btn-sm btn-danger" onclick="confirmCancel('{{ $participant['serial_number'] }}', '{{ $participant['full_name'] }}')">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        @else
                                            <span class="text-muted small">Not cancellable</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($bookingDetails['summary']['special_remarks'])
            <div class="alert alert-secondary">
                <strong>Special Remarks:</strong>
                <p class="mb-0 mt-2">{{ $bookingDetails['summary']['special_remarks'] }}</p>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title">Important Information</h5>
                    
                    @if($bookingDetails['important_info']['days_until_retreat'] !== null && $bookingDetails['important_info']['days_until_retreat'] > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-calendar-day"></i>
                        <strong>{{ $bookingDetails['important_info']['days_until_retreat'] }} days</strong> until retreat
                    </div>
                    @endif

                    <div class="mb-3">
                        <strong>Check-in:</strong>
                        <p class="mb-0">{{ $bookingDetails['important_info']['check_in_time'] }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Check-out:</strong>
                        <p class="mb-0">{{ $bookingDetails['important_info']['check_out_time'] }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Duration:</strong>
                        <p class="mb-0">{{ $bookingDetails['important_info']['duration_days'] }} days</p>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('booking.check-status') }}" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Check Another Booking
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                    </div>

                    @if($bookingDetails['important_info']['is_cancellable'])
                    <hr>
                    <div class="d-grid">
                        <button class="btn btn-danger" onclick="confirmCancelAll()">
                            <i class="fas fa-times-circle"></i> Cancel Entire Booking
                        </button>
                    </div>
                    <div class="alert alert-warning mt-3 mb-0">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            You can cancel individual participants or the entire booking. For assistance, contact 
                            <a href="mailto:support@myretreatbooking.com">support@myretreatbooking.com</a>
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmCancel(participantNumber, participantName) {
    if (confirm(`Are you sure you want to cancel the booking for ${participantName}?\n\nThis action cannot be undone.`)) {
        alert('Cancellation feature will be implemented. Please contact support at support@myretreatbooking.com for now.');
        // TODO: Implement cancellation API call
        // You can add AJAX call here to cancel individual participant
    }
}

function confirmCancelAll() {
    if (confirm('Are you sure you want to cancel the ENTIRE booking for all participants?\n\nThis action cannot be undone.')) {
        alert('Cancellation feature will be implemented. Please contact support at support@myretreatbooking.com for now.');
        // TODO: Implement cancellation API call
        // You can add AJAX call here to cancel entire booking
    }
}
</script>
@endpush
