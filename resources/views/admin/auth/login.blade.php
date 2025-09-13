@extends('admin.layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="my-0">{{ config('app.name') }}</h3>
                    <p class="mb-0">Admin Login</p>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mb-3" style="max-height: 80px;">
                        <h4 class="text-dark">Welcome Back!</h4>
                        <p class="text-muted">Sign in to continue to {{ config('app.name') }} Admin</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" 
                                       required autocomplete="email" autofocus
                                       placeholder="Enter your email">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                @if (Route::has('admin.password.request'))
                                    <a class="text-decoration-none" href="{{ route('admin.password.request') }}">
                                        {{ __('Forgot Password?') }}
                                    </a>
                                @endif
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="current-password"
                                       placeholder="Enter your password">
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" 
                                   id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i> {{ __('Login') }}
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">
                        <p class="mb-0">
                            <i class="fas fa-info-circle me-1"></i> For access, please contact system administrator.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
        background-image: url('{{ asset("images/auth-bg.jpg") }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 100vh;
    }
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .card-header {
        border-bottom: none;
        padding: 1.5rem;
    }
    .form-control, .input-group-text {
        border-radius: 0.375rem;
    }
    .input-group-text {
        background-color: #f8f9fa;
    }
    .btn-primary {
        padding: 0.6rem 1.5rem;
        font-weight: 500;
    }
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
@endpush
