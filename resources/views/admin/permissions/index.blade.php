@extends('admin.layouts.app')

@section('title', 'Permissions')

@section('content')
<div class="container-fluid">
    <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
            <h4 class="m-0 fw-bold" style="color: #b53d5e; font-size: 1.5rem;">Permissions</h4>
            @can('create-permissions')
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Create New Permission
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="permissionsTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Module</th>
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

@push('styles')
<style>
    /* Style all table headers */
    #permissionsTable th {
        font-weight: bold !important;
        background-color: #f8f9fc !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    /* Compact ID column styling */
    #permissionsTable th:first-child,
    #permissionsTable td:first-child {
        padding-left: 15px;
        width: 60px;
        min-width: 60px;
        max-width: 60px;
    }
    
    /* Adjust sort arrow position */
    #permissionsTable th.sorting:after,
    #permissionsTable th.sorting_asc:after,
    #permissionsTable th.sorting_desc:after {
        right: 4px;
    }
    
    /* Fix length menu dropdown styling */
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
<script>
    $(document).ready(function() {
        var table = $('#permissionsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('admin.permissions.index') }}",
                "type": "GET",
                "dataType": "json"
            },
            "columns": [
                { "data": "id", "name": "id", "width": "60px" },
                { "data": "name", "name": "name" },
                { "data": "slug", "name": "slug" },
                { "data": "module", "name": "module" },
                { 
                    "data": "status", 
                    "name": "is_active",
                    "orderable": true,
                    "searchable": false
                },
                { 
                    "data": "actions", 
                    "name": "actions",
                    "orderable": false,
                    "searchable": false,
                    "width": "100px"
                }
            ],
            "order": [[0, 'asc']],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "stateSave": true,
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search permissions...",
                "lengthMenu": "Show _MENU_ entries",
                "zeroRecords": "No matching records found",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No records available",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "<i class='fas fa-chevron-right'></i>",
                    "previous": "<i class='fas fa-chevron-left'></i>"
                }
            },
            "drawCallback": function(settings) {
                // Reinitialize tooltips after table draw
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });
</script>
@endpush
@endsection
