@extends('admin.layouts.app')

@section('title', 'Manage Retreats')

@section('content')
<div class="container-fluid">
    <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
            <h4 class="m-0 fw-bold" style="color: #b53d5e; font-size: 1.5rem;">Retreats</h4>
            <a href="{{ route('admin.retreats.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Create New Retreat
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
                        <table class="table table-bordered table-hover" id="retreats-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Timings</th>
                                    <th>Seats</th>
                                    <th>Criteria</th>
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
<style>
    /* Style all table headers */
    #retreats-table th {
        font-weight: bold !important;
        background-color: #f8f9fc !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    /* Compact ID column styling */
    #retreats-table th:first-child,
    #retreats-table td:first-child {
        padding-left: 15px;
    }
    
    .dataTables_length select {
        margin: 0 5px;
        padding: 4px 20px;
        border-radius: 4px;
        border: 1px solid #d1d3e2;
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#retreats-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.retreats.index') }}",
            type: "GET"
        },
        columns: [
            { data: 'title', name: 'title' },
            { data: 'date', name: 'date', orderable: false },
            { data: 'timings', name: 'timings' },
            { 
                data: 'seats', 
                name: 'seats',
                className: 'text-center'
            },
            { 
                data: 'criteria', 
                name: 'criteria',
                orderable: false
            },
            { 
                data: 'status', 
                name: 'is_active',
                orderable: true,
                searchable: false,
                className: 'text-center'
            },
            { 
                data: 'actions', 
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center',
                width: '120px'
            }
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-4'f><'col-sm-12 col-md-2'l>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: [
            {
                extend: 'excel',
                className: 'btn btn-sm btn-success',
                text: '<i class="fas fa-file-excel me-1"></i> Excel',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger',
                text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-secondary',
                text: '<i class="fas fa-print me-1"></i> Print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                },
                customize: function (win) {
                    $(win.document.body).find('h1').text('Retreats List');
                }
            }
        ],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            search: "_INPUT_",
            searchPlaceholder: "Search retreats...",
            lengthMenu: "Show _MENU_ entries",
            zeroRecords: "No matching retreats found",
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
            // Reinitialize tooltips after table draw
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    
    // Add custom search input
    $('.dataTables_filter').addClass('d-none');
    $('<div class="input-group mb-3" style="max-width: 300px;">' +
      '<input type="text" id="custom-search" class="form-control form-control-sm" placeholder="Search retreats...">' +
      '<button class="btn btn-outline-secondary btn-sm" type="button" id="search-btn"><i class="fas fa-search"></i></button>' +
      '</div>').insertBefore('#retreats-table_wrapper .row:first');
    
    $('#search-btn').on('click', function() {
        table.search($('#custom-search').val()).draw();
    });
    
    $('#custom-search').on('keyup', function(e) {
        if (e.key === 'Enter') {
            table.search(this.value).draw();
        }
    });
});
</script>
@endpush
