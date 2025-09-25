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
                                                  onsubmit="return confirm('Are you sure you want to delete this booking? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
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
        font-weight: bold !important;
        background-color: #f8f9fc !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
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
    }
    
    #bookings-table th,
    #bookings-table td {
        vertical-align: middle;
        word-wrap: break-word;
        padding: 12px 8px;
    }
    
    .guest-info {
        line-height: 1.4;
    }
    
    .guest-info strong {
        font-size: 14px;
        color: #333;
    }
    
    .guest-info small {
        font-size: 11px;
        display: block;
        margin: 2px 0;
    }
    
    .date-info {
        text-align: center;
        line-height: 1.3;
    }
    
    .date-info strong {
        font-size: 12px;
        color: #333;
    }
    
    .status-info .badge {
        font-size: 10px;
        padding: 4px 8px;
        display: inline-block;
        min-width: 70px;
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
            font-size: 12px;
        }
        
        .guest-info strong {
            font-size: 12px;
        }
        
        .guest-info small {
            font-size: 10px;
        }
        
        .btn-group-vertical .btn {
            padding: 2px 6px;
            font-size: 11px;
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#bookings-table').DataTable({
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
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "responsive": true,
            "order": [[0, 'desc']]
        });
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
