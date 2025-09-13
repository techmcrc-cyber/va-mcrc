@extends('admin.layouts.app')

@section('page-title', 'Email Settings')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.general') }}">Settings</a></li>
    <li class="breadcrumb-item active" aria-current="page">Email Settings</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.settings.update', 'email') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h5 class="mb-4">Mail Configuration</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_mailer" class="form-label">Mail Driver</label>
                                <select class="form-select" id="mail_mailer" name="mail_mailer">
                                    <option value="smtp" {{ $settings['mail_mailer'] == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="mailgun" {{ $settings['mail_mailer'] == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="ses" {{ $settings['mail_mailer'] == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    <option value="sendmail" {{ $settings['mail_mailer'] == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    <option value="mail" {{ $settings['mail_mailer'] == 'mail' ? 'selected' : '' }}>PHP Mail</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="mail_host" class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" id="mail_host" name="mail_host" 
                                       value="{{ old('mail_host', $settings['mail_host']) }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="mail_port" class="form-label">SMTP Port</label>
                                <input type="text" class="form-control" id="mail_port" name="mail_port" 
                                       value="{{ old('mail_port', $settings['mail_port']) }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="mail_username" class="form-label">SMTP Username</label>
                                <input type="text" class="form-control" id="mail_username" name="mail_username" 
                                       value="{{ old('mail_username', $settings['mail_username']) }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_password" class="form-label">SMTP Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="mail_password" name="mail_password" 
                                           value="{{ old('mail_password', $settings['mail_password']) }}" 
                                           placeholder="Leave blank to keep current">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="mail_encryption" class="form-label">Encryption</label>
                                <select class="form-select" id="mail_encryption" name="mail_encryption">
                                    <option value="tls" {{ $settings['mail_encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ $settings['mail_encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ empty($settings['mail_encryption']) ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="mail_from_address" class="form-label">From Email Address</label>
                                <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" 
                                       value="{{ old('mail_from_address', $settings['mail_from_address']) }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="mail_from_name" class="form-label">From Name</label>
                                <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" 
                                       value="{{ old('mail_from_name', $settings['mail_from_name']) }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Save Settings
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="testMailConfig()">
                            <i class="fas fa-paper-plane me-1"></i> Send Test Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Test Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="test_email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="test_email" placeholder="Enter email to send test">
                </div>
                <div id="testEmailResult" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="sendTestEmail">Send Test</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    $('.toggle-password').click(function() {
        const input = $(this).siblings('input');
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Test email configuration
    function testMailConfig() {
        $('#testEmailModal').modal('show');
    }
    
    $('#sendTestEmail').click(function() {
        const email = $('#test_email').val();
        if (!email) {
            $('#testEmailResult').html('<div class="alert alert-warning">Please enter an email address</div>');
            return;
        }
        
        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
        
        $.ajax({
            url: '{{ route("admin.settings.test-email") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                email: email
            },
            success: function(response) {
                if (response.success) {
                    $('#testEmailResult').html('<div class="alert alert-success">Test email sent successfully to ' + email + '</div>');
                } else {
                    $('#testEmailResult').html('<div class="alert alert-danger">' + (response.message || 'Failed to send test email') + '</div>');
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred while sending test email';
                $('#testEmailResult').html('<div class="alert alert-danger">' + error + '</div>');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
</script>
@endpush
