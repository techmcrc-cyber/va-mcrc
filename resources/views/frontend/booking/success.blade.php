@extends('frontend.layout')

@section('title', 'Booking Confirmed - Mount Carmel Retreat Centre')
@section('meta_description', 'Your retreat booking at Mount Carmel Retreat Centre has been confirmed. Check your email for booking details and prepare for your spiritual journey.')
@section('meta_keywords', 'booking confirmed, retreat confirmation, booking success, retreat booking confirmed, Mount Carmel booking, spiritual retreat confirmation')
@section('og_title', 'Booking Confirmed - Your Spiritual Journey Awaits')
@section('og_description', 'Congratulations! Your retreat booking has been confirmed. We look forward to welcoming you to Mount Carmel Retreat Centre.')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-success">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h1 class="text-success mb-3">Registration Successful!</h1>
                    <p class="lead">Your retreat booking has been confirmed</p>
                    
                    <div class="alert alert-info my-4">
                        <h4 class="alert-heading">Your Booking ID</h4>
                        <h2 class="mb-0"><strong>{{ $bookingData['booking_id'] }}</strong></h2>
                        <small class="text-muted">Please save this ID for future reference</small>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body text-start">
                            <h5 class="card-title">Retreat Details</h5>
                            <p class="mb-1"><strong>Retreat:</strong> {{ $bookingData['retreat']['name'] }}</p>
                            <p class="mb-1"><strong>Dates:</strong> {{ $bookingData['retreat']['start_date'] }} to {{ $bookingData['retreat']['end_date'] }}</p>
                            <p class="mb-0"><strong>Total Participants:</strong> {{ $bookingData['total_participants'] }}</p>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body text-start">
                            <h5 class="card-title">Registered Participants</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookingData['participants'] as $participant)
                                        <tr>
                                            <td>{{ $participant['serial_number'] }}</td>
                                            <td>{{ $participant['name'] }}</td>
                                            <td>{{ $participant['email'] }}</td>
                                            <td>
                                                <span class="badge {{ $participant['role'] == 'primary' ? 'bg-primary' : 'bg-secondary' }}">
                                                    {{ ucfirst($participant['role']) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-envelope"></i> A confirmation email has been sent to the primary participant's email address.
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="{{ route('booking.check-status') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-search"></i> Check Booking Status
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
