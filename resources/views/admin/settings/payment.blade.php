@extends('admin.layouts.app')

@section('page-title', 'Payment Settings')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.general') }}">Settings</a></li>
    <li class="breadcrumb-item active" aria-current="page">Payment Settings</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.settings.update', 'payment') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h5 class="mb-4">Payment Gateway Settings</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Default Currency</label>
                                <select class="form-select" id="currency" name="currency">
                                    <option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                    <option value="EUR" {{ $settings['currency'] == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                    <option value="GBP" {{ $settings['currency'] == 'GBP' ? 'selected' : '' }}>British Pound (GBP)</option>
                                    <option value="CAD" {{ $settings['currency'] == 'CAD' ? 'selected' : '' }}>Canadian Dollar (CAD)</option>
                                    <option value="AUD" {{ $settings['currency'] == 'AUD' ? 'selected' : '' }}>Australian Dollar (AUD)</option>
                                    <option value="INR" {{ $settings['currency'] == 'INR' ? 'selected' : '' }}>Indian Rupee (INR)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="currency_symbol" class="form-label">Currency Symbol</label>
                                <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" 
                                       value="{{ old('currency_symbol', $settings['currency_symbol']) }}" style="max-width: 100px;">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Mode</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_mode" id="payment_mode_sandbox" 
                                           value="sandbox" {{ $settings['payment_mode'] == 'sandbox' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_mode_sandbox">
                                        Sandbox (Testing)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_mode" id="payment_mode_live" 
                                           value="live" {{ $settings['payment_mode'] == 'live' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_mode_live">
                                        Live
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Configure your payment gateway credentials below. For security reasons, sensitive information is masked.
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-5 mb-4">Stripe Settings</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stripe_key" class="form-label">Publishable Key</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="stripe_key" name="stripe_key" 
                                           value="{{ old('stripe_key', $settings['stripe_key']) }}">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stripe_secret" class="form-label">Secret Key</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="stripe_secret" name="stripe_secret" 
                                           value="{{ old('stripe_secret', $settings['stripe_secret']) }}">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-5 mb-4">PayPal Settings</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paypal_client_id" class="form-label">Client ID</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="paypal_client_id" name="paypal_client_id" 
                                           value="{{ old('paypal_client_id', $settings['paypal_client_id']) }}">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paypal_secret" class="form-label">Secret Key</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="paypal_secret" name="paypal_secret" 
                                           value="{{ old('paypal_secret', $settings['paypal_secret']) }}">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Save Settings
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                    </div>
                </form>
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
    
    // Initialize select2 for better select inputs
    $(document).ready(function() {
        $('#currency').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endpush
