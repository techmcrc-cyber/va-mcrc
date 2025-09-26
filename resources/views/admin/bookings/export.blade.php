@extends('admin.layouts.app')

@section('title', 'Export Bookings')

@push('styles')
<style>
    .export-card {
        transition: transform 0.2s ease-in-out;
        border: 2px solid transparent;
    }
    .export-card:hover {
        transform: translateY(-2px);
        border-color: #007bff;
        box-shadow: 0 4px 15px rgba(0,123,255,.1);
    }
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.5rem;
    }
    .retreat-option {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .retreat-option:hover {
        border-color: #007bff;
        background-color: #f8f9fa;
    }
    .retreat-option.selected {
        border-color: #007bff;
        background-color: #e3f2fd;
    }
    .format-selector {
        background: #f8f9fa;
        border-radius: 0.375rem;
        padding: 1rem;
    }
    .format-option {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .format-option:hover {
        border-color: #007bff;
        background-color: white;
    }
    .format-option.selected {
        border-color: #007bff;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0,123,255,.1);
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
                        <i class="fas fa-file-export me-2"></i>Export Bookings
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Bookings
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Export Statistics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="stats-card p-3">
                                <h5 class="mb-3">
                                    <i class="fas fa-chart-bar me-2"></i>Export Statistics
                                </h5>
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <h4 class="mb-0">{{ $retreats->count() }}</h4>
                                            <small>Total Retreats</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <h4 class="mb-0" id="total-bookings">{{ $retreats->sum('bookings_count') }}</h4>
                                            <small>Total Active Bookings</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <h4 class="mb-0" id="upcoming-retreats">{{ $retreats->where('start_date', '>=', now())->count() }}</h4>
                                            <small>Upcoming Retreats</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <h4 class="mb-0" id="this-month">{{ $retreats->filter(function($retreat) { return $retreat->start_date->isCurrentMonth(); })->count() }}</h4>
                                            <small>This Month</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.bookings.export.process') }}" method="POST" id="export-form">
                        @csrf
                        
                        <div class="row">
                            <!-- Retreat Selection -->
                            <div class="col-md-8">
                                <div class="export-card card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="fas fa-calendar-alt me-2 text-primary"></i>Select Retreat(s) to Export
                                        </h6>
                                        
                                        <!-- All Retreats Option -->
                                        <div class="retreat-option" data-retreat-id="" onclick="selectRetreat(this, '')">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <i class="fas fa-list me-2 text-success"></i>All Retreats
                                                    </h6>
                                                    <small class="text-muted">Export bookings from all retreats</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success">{{ $retreats->sum('bookings_count') }} bookings</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Individual Retreat Options -->
                                        <div class="retreat-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.5rem;">
                                            @foreach($retreats as $retreat)
                                                <div class="retreat-option" data-retreat-id="{{ $retreat->id }}" 
                                                     onclick="selectRetreat(this, '{{ $retreat->id }}')">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">{{ $retreat->title }}</h6>
                                                            <div class="d-flex align-items-center gap-3">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-calendar me-1"></i>
                                                                    {{ $retreat->start_date->format('M d, Y') }} - {{ $retreat->end_date->format('M d, Y') }}
                                                                </small>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-users me-1"></i>
                                                                    {{ $retreat->getCriteriaLabelAttribute() }}
                                                                </small>
                                                                @if($retreat->start_date->isFuture())
                                                                    <span class="badge bg-info">Upcoming</span>
                                                                @elseif($retreat->start_date->isPast() && $retreat->end_date->isFuture())
                                                                    <span class="badge bg-warning">Ongoing</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Completed</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge {{ $retreat->bookings_count > 0 ? 'bg-primary' : 'bg-light text-dark' }}">
                                                                {{ $retreat->bookings_count }} booking{{ $retreat->bookings_count !== 1 ? 's' : '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <input type="hidden" name="retreat_id" id="selected_retreat_id" value="">
                                    </div>
                                </div>
                            </div>

                            <!-- Export Options -->
                            <div class="col-md-4">
                                <div class="export-card card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="fas fa-cog me-2 text-warning"></i>Export Options
                                        </h6>

                                        <!-- File Format -->
                                        <div class="format-selector mb-4">
                                            <label class="form-label">Export Format</label>
                                            <div class="format-option selected" data-format="xlsx" onclick="selectFormat(this, 'xlsx')">
                                                <i class="fas fa-file-excel text-success me-2"></i>
                                                <div>
                                                    <strong>Excel (.xlsx)</strong>
                                                    <small class="d-block text-muted">Best for data analysis</small>
                                                </div>
                                            </div>
                                            <div class="format-option" data-format="csv" onclick="selectFormat(this, 'csv')">
                                                <i class="fas fa-file-csv text-info me-2"></i>
                                                <div>
                                                    <strong>CSV (.csv)</strong>
                                                    <small class="d-block text-muted">Universal format</small>
                                                </div>
                                            </div>
                                            <input type="hidden" name="export_format" id="export_format" value="xlsx">
                                        </div>


                                        <!-- Selection Summary -->
                                        <div class="alert alert-light no-auto-hide" id="selection-summary">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-info-circle me-2"></i>Selection Summary
                                            </h6>
                                            <p class="mb-0" id="summary-text">Please select a retreat to export</p>
                                        </div>

                                        <!-- Export Button -->
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-success btn-lg" id="export-btn" disabled>
                                                <i class="fas fa-download me-2"></i>Export Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Define global functions before document ready
function updateSummary(retreatId) {
    var summaryText = '';
    var bookingCount = 0;

    if (retreatId === '') {
        summaryText = 'Exporting all bookings from all retreats';
        bookingCount = {{ $retreats->sum('bookings_count') }};
    } else {
        var selectedOption = $('.retreat-option[data-retreat-id="' + retreatId + '"]');
        var retreatTitle = selectedOption.find('h6').first().text().trim();
        var badgeText = selectedOption.find('.badge').last().text().trim();
        
        // Extract number from badge text (e.g., "5 bookings" -> 5)
        var matches = badgeText.match(/^(\d+)/);
        bookingCount = matches ? parseInt(matches[1]) : 0;
        
        summaryText = 'Exporting bookings from: ' + retreatTitle;
    }

    $('#summary-text').html(summaryText + '<br><strong>' + bookingCount + ' booking(s)</strong> will be exported');
    $('#export-btn').prop('disabled', bookingCount === 0);
}

// Global functions for onclick handlers
window.selectRetreat = function(element, retreatId) {
    // Remove selected class from all retreat options
    $('.retreat-option').removeClass('selected');
    
    // Add selected class to clicked option
    $(element).addClass('selected');
    
    // Update hidden input
    $('#selected_retreat_id').val(retreatId);
    
    // Update summary
    updateSummary(retreatId);
};

window.selectFormat = function(element, format) {
    // Remove selected class from all format options
    $('.format-option').removeClass('selected');
    
    // Add selected class to clicked option
    $(element).addClass('selected');
    
    // Update hidden input
    $('#export_format').val(format);
};

$(document).ready(function() {
    // Initialize with "All Retreats" selected
    selectRetreat($('.retreat-option[data-retreat-id=""]')[0], '');

    // Form submission
    $('#export-form').on('submit', function() {
        $('#export-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Preparing Export...');
        
        // Re-enable button after a delay (in case of quick download)
        setTimeout(function() {
            $('#export-btn').prop('disabled', false).html('<i class="fas fa-download me-2"></i>Export Data');
        }, 3000);
    });

    // Optional: Add search functionality for retreats
    var searchTimeout;
    function addSearchBox() {
        var searchHtml = `
            <div class="mb-3">
                <input type="text" class="form-control" id="retreat-search" placeholder="Search retreats...">
            </div>
        `;
        $('.card-body h6:contains("Select Retreat")').after(searchHtml);

        $('#retreat-search').on('input', function() {
            clearTimeout(searchTimeout);
            var searchTerm = $(this).val().toLowerCase();
            
            searchTimeout = setTimeout(function() {
                $('.retreat-option[data-retreat-id!=""]').each(function() {
                    var retreatTitle = $(this).find('h6').text().toLowerCase();
                    if (retreatTitle.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }, 300);
        });
    }

    // Add search box if there are many retreats
    if ($('.retreat-option[data-retreat-id!=""]').length > 10) {
        addSearchBox();
    }
});
</script>
@endpush