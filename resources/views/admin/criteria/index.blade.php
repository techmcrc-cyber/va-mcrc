@extends('admin.layouts.app')

@section('title', 'Manage Criteria')

@section('content')
<div class="container-fluid">
    <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
            <h4 class="m-0 fw-bold" style="color: #b53d5e; font-size: 1.5rem;">Criteria</h4>
            <a href="{{ route('admin.criteria.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Create New Criteria
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
                        <table class="table table-bordered table-hover" id="criteria-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Age Range</th>
                                    <th>Married</th>
                                    <th>Vocation</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
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
    #criteria-table th {
        font-weight: bold !important;
        background-color: #f8f9fc !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    .dataTables_length {
        margin-bottom: 15px;
    }
    
    .dataTables_length select {
        margin: 0 5px;
        padding: 4px 20px;
        border-radius: 4px;
        border: 1px solid #d1d3e2;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#criteria-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.criteria.index') }}",
        columns: [
            { data: 'name', name: 'name' },
            { data: 'gender', name: 'gender' },
            { data: 'age_range', name: 'age_range', orderable: false },
            { data: 'married', name: 'married' },
            { data: 'vocation', name: 'vocation' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center', width: '100px' }
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        stateSave: true,
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search criteria...",
            lengthMenu: "Show _MENU_ entries",
            zeroRecords: "No matching criteria found",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No criteria available",
            infoFiltered: "(filtered from _MAX_ total entries)",
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
            paginate: {
                first: "First",
                last: "Last",
                next: "<i class='fas fa-chevron-right'></i>",
                previous: "<i class='fas fa-chevron-left'></i>"
            }
        },
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
});
</script>
@endpush
