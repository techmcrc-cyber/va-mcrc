@extends('admin.layouts.app')

@section('title', 'Import Preview')

@push('styles')
<style>
    .preview-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
    }
    .status-success {
        background-color: #d4edda;
        border-left: 4px solid #28a745;
    }
    .status-error {
        background-color: #f8d7da;
        border-left: 4px solid #dc3545;
    }
    .status-warning {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }
    .preview-table {
        font-size: 0.875rem;
    }
    .preview-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: none;
    }
    .preview-table td {
        vertical-align: middle;
    }
    .error-list {
        max-height: 150px;
        overflow-y: auto;
    }
    .stats-row {
        background: #f8f9fa;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .criteria-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .import-actions {
        position: sticky;
        bottom: 0;
        background: white;
        border-top: 1px solid #dee2e6;
        padding: 1rem;
        margin: -1rem;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye me-2"></i>Import Preview
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bookings.import') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Import
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Preview Header -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="preview-header">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-2">
                                            <i class="fas fa-file-alt me-2"></i>Import Preview for: {{ $retreat->title }}
                                        </h5>
                                        <div class="d-flex align-items-center gap-3">
                                            <span>
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $retreat->start_date->format('M d, Y') }} - {{ $retreat->end_date->format('M d, Y') }}
                                            </span>
                                            <span>
                                                <i class="fas fa-users me-1"></i>
                                                {{ $retreat->getCriteriaLabelAttribute() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <h4 class="mb-0">{{ count($previewData) }}</h4>
                                        <small>Records Found</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    @php
                        $successCount = collect($previewData)->where('status', 'success')->count();
                        $errorCount = collect($previewData)->where('status', 'error')->count();
                        $warningCount = collect($previewData)->where('validation.warnings')->flatten()->count();
                    @endphp
                    
                    <div class="stats-row">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="me-3">
                                        <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 text-success">{{ $successCount }}</h4>
                                        <small class="text-muted">Valid Records</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="me-3">
                                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 2rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 text-danger">{{ $errorCount }}</h4>
                                        <small class="text-muted">Invalid Records</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="me-3">
                                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 text-warning">{{ $warningCount }}</h4>
                                        <small class="text-muted">Warnings</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="me-3">
                                        <i class="fas fa-percentage text-info" style="font-size: 2rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 text-info">{{ count($previewData) > 0 ? round(($successCount / count($previewData)) * 100) : 0 }}%</h4>
                                        <small class="text-muted">Success Rate</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary active" data-filter="all">
                                    All Records ({{ count($previewData) }})
                                </button>
                                <button type="button" class="btn btn-outline-success" data-filter="success">
                                    Valid ({{ $successCount }})
                                </button>
                                <button type="button" class="btn btn-outline-danger" data-filter="error">
                                    Errors ({{ $errorCount }})
                                </button>
                                <button type="button" class="btn btn-outline-warning" data-filter="warning">
                                    With Warnings ({{ collect($previewData)->filter(function($item) { return !empty($item['validation']['warnings']); })->count() }})
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover preview-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">Row</th>
                                    <th style="width: 80px;">Status</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>City</th>
                                    <th>Congregation</th>
                                    <th>Issues</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previewData as $item)
                                    @php
                                        $data = $item['data'];
                                        $validation = $item['validation'];
                                        $hasWarnings = !empty($validation['warnings']);
                                        $statusClass = $item['status'] === 'success' ? 'status-success' : 'status-error';
                                        if ($item['status'] === 'success' && $hasWarnings) {
                                            $statusClass = 'status-warning';
                                        }
                                    @endphp
                                    <tr class="preview-row {{ $statusClass }}" 
                                        data-status="{{ $item['status'] }}" 
                                        data-has-warnings="{{ $hasWarnings ? 'true' : 'false' }}">
                                        <td><strong>{{ $item['row_number'] }}</strong></td>
                                        <td>
                                            @if($item['status'] === 'success')
                                                @if($hasWarnings)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> Warning
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Valid
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times"></i> Error
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $data['firstname'] }} {{ $data['lastname'] }}</strong>
                                        </td>
                                        <td>{{ $data['email'] ?: '-' }}</td>
                                        <td>{{ $data['whatsapp_number'] ? '+91 ' . $data['whatsapp_number'] : '-' }}</td>
                                        <td>{{ $data['age'] ?: '-' }}</td>
                                        <td>
                                            @if($data['gender'])
                                                <span class="badge bg-light text-dark">{{ ucfirst($data['gender']) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $data['city'] ?: '-' }}</td>
                                        <td>
                                            @if($data['congregation'])
                                                <span class="criteria-badge badge bg-info">{{ $data['congregation'] }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($validation['errors']))
                                                <div class="error-list">
                                                    @foreach($validation['errors'] as $error)
                                                        <small class="d-block text-danger">
                                                            <i class="fas fa-times-circle me-1"></i>{{ $error }}
                                                        </small>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if(!empty($validation['warnings']))
                                                <div class="error-list">
                                                    @foreach($validation['warnings'] as $warning)
                                                        <small class="d-block text-warning">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>{{ $warning }}
                                                        </small>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if(empty($validation['errors']) && empty($validation['warnings']))
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>No issues
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Import Actions -->
                    <div class="import-actions">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                @if($successCount > 0)
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>{{ $successCount }}</strong> valid records will be imported. 
                                        @if($errorCount > 0)
                                            <strong>{{ $errorCount }}</strong> records with errors will be skipped.
                                        @endif
                                    </div>
                                @else
                                    <div class="alert alert-danger mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No valid records found. Please fix the errors and try again.
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.bookings.import') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back
                                    </a>
                                    @if($successCount > 0)
                                        <form action="{{ route('admin.bookings.import.confirm') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="return confirmImport()">
                                                <i class="fas fa-upload me-2"></i>Confirm Import ({{ $successCount }} records)
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Filter functionality
    $('[data-filter]').on('click', function() {
        var filter = $(this).data('filter');
        
        // Update active button
        $('[data-filter]').removeClass('active');
        $(this).addClass('active');
        
        // Filter rows
        $('.preview-row').hide();
        
        if (filter === 'all') {
            $('.preview-row').show();
        } else if (filter === 'success') {
            $('.preview-row[data-status="success"][data-has-warnings="false"]').show();
        } else if (filter === 'error') {
            $('.preview-row[data-status="error"]').show();
        } else if (filter === 'warning') {
            $('.preview-row[data-has-warnings="true"]').show();
        }
    });

    // Table row hover effects
    $('.preview-row').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );

    // Auto-scroll to first error if exists
    var firstError = $('.preview-row[data-status="error"]').first();
    if (firstError.length > 0 && $('.preview-row[data-status="success"]').length === 0) {
        $('html, body').animate({
            scrollTop: firstError.offset().top - 100
        }, 1000);
    }
});

function confirmImport() {
    var successCount = {{ $successCount }};
    var errorCount = {{ $errorCount }};
    var warningCount = {{ collect($previewData)->filter(function($item) { return !empty($item['validation']['warnings']); })->count() }};
    
    var message = `Are you sure you want to import ${successCount} booking(s)?`;
    
    if (errorCount > 0) {
        message += `\n\n${errorCount} records with errors will be skipped.`;
    }
    
    if (warningCount > 0) {
        message += `\n\n${warningCount} records have warnings but will still be imported.`;
    }
    
    message += '\n\nThis action cannot be undone.';
    
    return confirm(message);
}
</script>
@endpush