@extends('admin.layouts.app')

@section('page-title', 'General Settings')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">General Settings</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.settings.update', 'general') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-4">Site Information</h5>
                            
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Site Name</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" 
                                       value="{{ old('site_name', $settings['site_name']) }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_description" class="form-label">Site Description</label>
                                <textarea class="form-control" id="site_description" name="site_description" 
                                          rows="3">{{ old('site_description', $settings['site_description']) }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="site_email" name="site_email" 
                                       value="{{ old('site_email', $settings['site_email']) }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_phone" class="form-label">Contact Phone</label>
                                <input type="text" class="form-control" id="site_phone" name="site_phone" 
                                       value="{{ old('site_phone', $settings['site_phone']) }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_address" class="form-label">Address</label>
                                <textarea class="form-control" id="site_address" name="site_address" 
                                          rows="2">{{ old('site_address', $settings['site_address']) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="mb-4">Date & Time</h5>
                            
                            <div class="mb-3">
                                <label for="timezone" class="form-label">Timezone</label>
                                <select class="form-select" id="timezone" name="timezone">
                                    @foreach(timezone_identifiers_list() as $timezone)
                                        <option value="{{ $timezone }}" {{ $settings['timezone'] == $timezone ? 'selected' : '' }}>
                                            {{ $timezone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="date_format" class="form-label">Date Format</label>
                                <select class="form-select" id="date_format" name="date_format">
                                    <option value="Y-m-d" {{ $settings['date_format'] == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD ({{ date('Y-m-d') }})</option>
                                    <option value="d/m/Y" {{ $settings['date_format'] == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY ({{ date('d/m/Y') }})</option>
                                    <option value="m/d/Y" {{ $settings['date_format'] == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY ({{ date('m/d/Y') }})</option>
                                    <option value="d M Y" {{ $settings['date_format'] == 'd M Y' ? 'selected' : '' }}>DD MMM YYYY ({{ date('d M Y') }})</option>
                                    <option value="M d, Y" {{ $settings['date_format'] == 'M d, Y' ? 'selected' : '' }}>MMM DD, YYYY ({{ date('M d, Y') }})</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="time_format" class="form-label">Time Format</label>
                                <select class="form-select" id="time_format" name="time_format">
                                    <option value="H:i" {{ $settings['time_format'] == 'H:i' ? 'selected' : '' }}>24-hour ({{ date('H:i') }})</option>
                                    <option value="h:i A" {{ $settings['time_format'] == 'h:i A' ? 'selected' : '' }}>12-hour ({{ date('h:i A') }})</option>
                                </select>
                            </div>
                            
                            <div class="mt-5 pt-3">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i> Save Settings
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize select2 for better select inputs
    $(document).ready(function() {
        $('#timezone').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endpush
