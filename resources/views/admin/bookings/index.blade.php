@extends('admin.layouts.app')

@section('title', 'Retreat Bookings')

@section('content')
<div class="container-fluid">
    <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
            <h4 class="m-0 fw-bold" style="color: #b53d5e; font-size: 1.5rem;">Retreat Bookings</h4>
            <a href="{{ route('admin.bookings.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Create New Booking
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="bookings-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 12%;">Booking ID</th>
                                <th style="width: 18%;">Retreat</th>
                                <th style="width: 20%;">Primary Guest & Contact</th>
                                <th style="width: 18%;">Dates</th>
                                <th style="width: 10%;">Participants</th>
                                <th style="width: 12%;">Status</th>
                                <th style="width: 10%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->booking_id }}</td>
                                    <td>{{ $booking->retreat->title }}</td>
                                    <td>
                                        <div class="guest-info">
                                            <strong>{{ $booking->firstname }} {{ $booking->lastname }}</strong>
                                            @if($booking->flag)
                                                <span class="badge bg-warning ml-1" data-toggle="tooltip" title="{{ $booking->flag }}">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-phone-alt"></i> {{ $booking->whatsapp_number }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope"></i> {{ $booking->email }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-info">
                                            <strong>{{ $booking->retreat->start_date->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">to</small>
                                            <br>
                                            <strong>{{ $booking->retreat->end_date->format('M d, Y') }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $booking->additional_participants + 1 }}</span>
                                        @if($booking->additional_participants > 0)
                                            <br><small class="text-muted">(+{{ $booking->additional_participants }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="status-info">
                                            @if($booking->flag)
                                                @php
                                                    $flags = explode(',', $booking->flag);
                                                @endphp
                                                @foreach($flags as $flag)
                                                    <div class="mb-1">
                                                        <span class="badge bg-warning">
                                                            {{ Str::title(str_replace('_', ' ', trim($flag))) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="badge bg-success">Confirmed</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical" role="group">
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}" 
                                               class="btn btn-sm btn-info mb-1" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.bookings.edit', $booking->id) }}" 
                                               class="btn btn-sm btn-primary mb-1" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.bookings.destroy', $booking->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to cancel this booking? This will deactivate all participants in this booking.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Cancel Booking">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No bookings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    /* Style all table headers */
    #bookings-table th {
        font-weight: normal !important;
        background-color: #f8f9fc !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        font-size: 13px !important; /* Further reduced header font size */
    }
    
    /* Compact ID column styling */
    #bookings-table th:first-child,
    #bookings-table td:first-child {
        padding-left: 15px;
    }
    
    .dataTables_length select {
        margin: 0 5px;
        padding: 4px 20px;
        border-radius: 4px;
        border: 1px solid #d1d3e2;
    }
    /* Custom styling for bookings table */
    #bookings-table {
        table-layout: fixed;
        width: 100%;
        font-size: 15px; /* Base font size increased */
    }
    
    #bookings-table th,
    #bookings-table td {
        vertical-align: middle;
        word-wrap: break-word;
        padding: 12px 8px;
        font-size: 15px; /* Increased from default */
    }
    
    .guest-info {
        line-height: 1.4;
    }
    
    .guest-info strong {
        font-size: 16px; /* Increased from 14px */
        color: #333;
        font-weight: 600;
    }
    
    .guest-info small {
        font-size: 13px; /* Increased from 11px */
        display: block;
        margin: 2px 0;
    }
    
    .date-info {
        text-align: center;
        line-height: 1.3;
    }
    
    .date-info strong {
        font-size: 15px; /* Increased from 12px */
        color: #333;
        font-weight: normal; /* Removed bold formatting */
    }
    
    .status-info .badge {
        font-size: 12px; /* Increased from 10px */
        padding: 6px 10px; /* Increased padding */
        display: inline-block;
        min-width: 75px;
        font-weight: 500;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 3px;
        min-width: 35px;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        #bookings-table th,
        #bookings-table td {
            padding: 8px 4px;
            font-size: 14px; /* Increased from 12px */
        }
        
        .guest-info strong {
            font-size: 15px; /* Increased from 12px */
        }
        
        .guest-info small {
            font-size: 12px; /* Increased from 10px */
        }
        
        .btn-group-vertical .btn {
            padding: 2px 6px;
            font-size: 12px; /* Increased from 11px */
        }
        
        .status-info .badge {
            font-size: 11px; /* Increased for mobile */
        }
    }
    
    /* Badge improvements */
    .badge {
        font-weight: 500;
    }
    
    .bg-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }
    
    .bg-success {
        background-color: #28a745 !important;
        color: white;
    }
    
    .bg-primary {
        background-color: #007bff !important;
        color: white;
    }
    
    /* Filter section styling */
    #retreat-filter {
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
        padding: 0.5rem 0.75rem;
        font-size: 14px;
        background-color: white;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    #retreat-filter:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #495057;
    }
    
    /* DataTables controls alignment */
    .dataTables_wrapper .row:first-child {
        margin-bottom: 1rem;
    }
    
    .dataTables_wrapper .row:first-child > div {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        min-height: 70px;
    }
    
    .dataTables_length {
        margin-bottom: 0;
    }
    
    .dataTables_filter {
        margin-bottom: 0;
        text-align: right;
    }
    
    .retreat-filter-container {
        margin-bottom: 0;
    }
    
    /* Ensure consistent spacing on mobile */
    @media (max-width: 767px) {
        .dataTables_wrapper .row:first-child > div {
            min-height: auto;
            margin-bottom: 0.5rem;
        }
        
        .dataTables_filter {
            text-align: left;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#bookings-table').DataTable({
            "pageLength": 25,
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search bookings...",
                "lengthMenu": "Show _MENU_ entries",
                "zeroRecords": "No matching records found",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries available",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "dom": "<'row'<'col-md-4'l><'col-md-4'<'retreat-filter-container'>><'col-md-4'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "responsive": true,
            "order": [[0, 'desc']],
            "initComplete": function() {
                // Add retreat filter to custom container
                var retreatFilterHtml = `
                    <div class="form-group mb-0">
                        <select id="retreat-filter" class="form-control">
                            <option value="">All Retreats</option>
                            @foreach($retreats as $retreat)
                                <option value="{{ $retreat->title }}">
                                    {{ $retreat->title }} ({{ $retreat->start_date->format('M Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                `;
                $('.retreat-filter-container').html(retreatFilterHtml);
                
                // Initialize retreat filter functionality
                $('#retreat-filter').on('change', function() {
                    var selectedRetreat = this.value;
                    if (selectedRetreat === '') {
                        table.column(1).search('').draw(); // Clear filter if "All Retreats" selected
                    } else {
                        table.column(1).search(selectedRetreat).draw(); // Filter by retreat name (column index 1)
                    }
                });
            }
        });
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
