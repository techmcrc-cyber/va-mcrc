@extends('admin.layouts.app')

@section('title', 'Archived Retreat Bookings')

@section('content')
<div class="container-fluid">
    <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
            <h4 class="m-0 fw-bold" style="color: #b53d5e; font-size: 1.5rem;">Archived Retreat Bookings</h4>
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
                                    <th>Booking ID</th>
                                    <th>Retreat</th>
                                    <th>Primary Guest & Contact</th>
                                    <th>Participants</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
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
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.4/css/select.dataTables.min.css">
<style>
    /* Style all table headers */
    #bookings-table th {
        font-weight: normal !important;
        background-color: #f8f9fc !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        font-size: 13px !important;
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
        font-size: 15px;
    }
    
    #bookings-table th,
    #bookings-table td {
        vertical-align: middle;
        word-wrap: break-word;
        padding: 8px 6px;
        font-size: 14px;
    }
    
    .guest-info {
        line-height: 1.3;
        padding: 0;
    }
    
    .guest-name {
        margin-bottom: 4px;
    }
    
    .guest-info strong {
        font-size: 15px;
        color: #333;
        font-weight: 600;
        line-height: 1.2;
    }
    
    .guest-contact {
        margin-top: 3px;
    }
    
    /* Loading overlay */
    .dataTables_processing {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        z-index: 1;
    }
    
    /* Button styles */
    .dt-buttons .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    .guest-contact small {
        font-size: 12px;
        line-height: 1.3;
        margin: 1px 0;
    }
    
    .guest-contact small.d-block {
        margin-bottom: 2px;
    }
    
    .status-info .badge {
        font-size: 12px;
        padding: 6px 10px;
        display: inline-block;
        min-width: 75px;
        font-weight: 500;
    }
    
    .action-buttons {
        width: 100%;
        max-width: 80px;
    }
    
    .btn-row {
        display: flex;
        justify-content: center;
        gap: 2px;
    }
    
    .action-buttons .btn {
        padding: 5px 6px;
        min-width: 32px;
        font-size: 12px;
        line-height: 1.2;
        border-radius: 3px;
    }
    
    .action-buttons .btn-sm {
        padding: 4px 5px;
        font-size: 11px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        #bookings-table th,
        #bookings-table td {
            padding: 6px 4px;
            font-size: 13px;
        }
        
        .guest-info strong {
            font-size: 14px;
        }
        
        .guest-contact small {
            font-size: 11px;
        }
        
        .action-buttons .btn {
            padding: 3px 4px;
            font-size: 10px;
            min-width: 28px;
        }
        
        .action-buttons {
            max-width: 70px;
        }
        
        .status-info .badge {
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
    
    #retreat-filter, #status-filter, #custom-search {
        padding: 0.5rem 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#bookings-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.bookings.archive') }}",
            type: "GET",
            data: function(d) {
                d.retreat_filter = $('#retreat-filter').val();
                d.status_filter = $('#status-filter').val();
            }
        },
        columns: [
            { data: 'booking_id', name: 'booking_id', width: '10%' },
            { data: 'retreat', name: 'retreat.title', width: '25%' },
            { data: 'guest_info', name: 'firstname', width: '30%', orderable: false, searchable: false },
            { data: 'participants', name: 'additional_participants', width: '10%', className: 'text-center', orderable: true, searchable: false },
            { data: 'status', name: 'flag', width: '15%', orderable: true, searchable: false },
            { data: 'actions', name: 'actions', width: '10%', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            search: "_INPUT_",
            searchPlaceholder: "Search bookings...",
            lengthMenu: "Show _MENU_ entries",
            zeroRecords: "No matching bookings found",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries available",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "<i class='fas fa-chevron-right'></i>",
                previous: "<i class='fas fa-chevron-left'></i>"
            }
        },
        responsive: true,
        drawCallback: function() {
            $('[data-toggle="tooltip"]').tooltip();
        },
        initComplete: function() {
            var filterHtml = `
                <div class="row mb-3 g-2">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Retreat:</span>
                            <select id="retreat-filter" class="form-select form-select-sm">
                                <option value="">All Retreats</option>
                                @foreach($retreats as $retreat)
                                    <option value="{{ $retreat->title }}">
                                        {{ $retreat->title }} ({{ $retreat->start_date->format('M Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Status:</span>
                            <select id="status-filter" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="pending">Pending</option>
                                <option value="CRITERIA_FAILED">Criteria Failed</option>
                                <option value="RECURRENT_BOOKING">Recurrent Booking</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Search:</span>
                            <input type="search" id="custom-search" class="form-control form-control-sm" placeholder="Search bookings...">
                        </div>
                    </div>
                </div>
            `;
            
            $('.dataTables_filter').addClass('d-none');
            $('.dataTables_length').addClass('d-none');
            $(filterHtml).insertBefore('#bookings-table_wrapper .row:first');
            
            $('#retreat-filter, #status-filter').on('change', function() {
                table.ajax.reload();
            });
            
            var searchTimeout;
            $('#custom-search').on('keyup', function() {
                clearTimeout(searchTimeout);
                var searchValue = this.value;
                searchTimeout = setTimeout(function() {
                    table.search(searchValue).draw();
                }, 500);
            });
        }
    });
    
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
