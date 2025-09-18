@extends('admin.layouts.app')

@section('title', 'Permissions')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Permissions</h1>
        @can('create-permissions')
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Create New Permission
        </a>
        @endcan
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
                        @foreach($permissions as $permission)
                            <tr>
                                <td>{{ $permission->id }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->slug }}</td>
                                <td>{{ $permission->module ?: 'N/A' }}</td>
                                <td>
                                    @if($permission->is_active)
                                        <span class="badge bg-success text-white">
                                            <i class="fas fa-check-circle me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white">
                                            <i class="fas fa-times-circle me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($permission->is_deletable)
                                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this permission? This action cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
        $('#permissionsTable').DataTable({
            "pageLength": 25,
            "columnDefs": [
                { 
                    "orderable": false, 
                    "targets": [5] // Disable sorting on Actions column
                },
                {
                    "targets": 0, // ID column
                    "width": '60px',
                    "className": 'dt-body-left'
                }
            ],
            "order": [[0, 'asc']], // Default sort by ID column
            "stateSave": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search permissions...",
                "lengthMenu": "Show _MENU_ entries",
                "zeroRecords": "No matching records found",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No records available",
                "infoFiltered": "(filtered from _MAX_ total)"
            },
            "responsive": true
        });
    });
</script>
@endpush
@endsection
