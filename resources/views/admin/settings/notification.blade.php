@extends('admin.layouts.app')

@section('page-title', 'Notification Settings')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.general') }}">Settings</a></li>
    <li class="breadcrumb-item active" aria-current="page">Notification Settings</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.settings.update', 'notification') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h5 class="mb-4">Notification Preferences</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Notification Methods</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="notifications_enabled" name="notifications_enabled" value="1" 
                                               {{ $settings['notifications_enabled'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="notifications_enabled">
                                            Enable Notifications
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="email_notifications" name="email_notifications" value="1"
                                               {{ $settings['email_notifications'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_notifications">
                                            Email Notifications
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="sms_notifications" name="sms_notifications" value="1"
                                               {{ $settings['sms_notifications'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sms_notifications">
                                            SMS Notifications
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="push_notifications" name="push_notifications" value="1"
                                               {{ $settings['push_notifications'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="push_notifications">
                                            Push Notifications
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Notification Types</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="booking_confirmation" name="booking_confirmation" value="1"
                                               {{ $settings['booking_confirmation'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="booking_confirmation">
                                            Booking Confirmations
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="payment_received" name="payment_received" value="1"
                                               {{ $settings['payment_received'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_received">
                                            Payment Receipts
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="upcoming_event" name="upcoming_event" value="1"
                                               {{ $settings['upcoming_event'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="upcoming_event">
                                            Upcoming Event Reminders
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Email Templates</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="#" class="btn btn-outline-primary btn-sm text-start">
                                            <i class="far fa-envelope me-2"></i> Booking Confirmation Template
                                        </a>
                                        <a href="#" class="btn btn-outline-primary btn-sm text-start">
                                            <i class="far fa-envelope me-2"></i> Payment Receipt Template
                                        </a>
                                        <a href="#" class="btn btn-outline-primary btn-sm text-start">
                                            <i class="far fa-envelope me-2"></i> Password Reset Template
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Save Settings
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-1"></i> Reset to Defaults
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Test Notifications</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-primary" onclick="sendTestNotification('email')">
                                <i class="fas fa-envelope me-2"></i> Send Test Email
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-info" onclick="sendTestNotification('sms')">
                                <i class="fas fa-sms me-2"></i> Send Test SMS
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-secondary" onclick="sendTestNotification('push')">
                                <i class="fas fa-bell me-2"></i> Send Test Push
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="testNotificationResult" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function sendTestNotification(type) {
        const btn = $(`button:contains('Send Test ${type.toUpperCase()}')`);
        const originalText = btn.html();
        const resultDiv = $('#testNotificationResult');
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
        resultDiv.html('');
        
        // Simulate API call (replace with actual API call)
        setTimeout(() => {
            resultDiv.html(`
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    Test ${type} notification sent successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);
            
            btn.prop('disabled', false).html(originalText);
        }, 1500);
    }
    
    // Toggle child checkboxes when parent is toggled
    $('#notifications_enabled').change(function() {
        const isChecked = $(this).is(':checked');
        $('input[type="checkbox"]').not(this).prop('disabled', !isChecked);
        
        if (!isChecked) {
            $('input[type="checkbox"]').not(this).prop('checked', false);
        }
    });
    
    // Initial state
    $(document).ready(function() {
        if (!$('#notifications_enabled').is(':checked')) {
            $('input[type="checkbox"]').not('#notifications_enabled').prop('disabled', true);
        }
    });
</script>
@endpush
