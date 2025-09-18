@extends('admin.layouts.app')

@section('title', 'Users')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Users</h1>
        @can('create-users')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Create New User
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
                <table class="table table-bordered table-hover" id="usersTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role)
                                        @php
                                            $roleName = $user->role->name;
                                            list($bgColor, $textColor, $borderColor) = \App\Helpers\RoleHelper::getRoleColors($roleName);
                                            $isSuperAdmin = $user->isSuperAdmin();
                                        @endphp
                                        <span class="badge shadow-sm" 
                                              style="background-color: {{ $bgColor }}; 
                                                     color: {{ $textColor }}; 
                                                     border: 1px solid {{ $borderColor }};
                                                     @if($isSuperAdmin) 
                                                         background: linear-gradient(135deg, {{ $bgColor }} 0%, {{ $borderColor }} 100%);
                                                     @endif">
                                            @if($isSuperAdmin)
                                                <i class="fas fa-crown me-1"></i>
                                            @else
                                                <i class="fas fa-user-shield me-1"></i>
                                            @endif
                                            {{ $roleName }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-user-slash me-1"></i> No Role
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times-circle me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$user->isSuperAdmin() && auth()->id() !== $user->id)
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
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
    #usersTable th:first-child,
    #usersTable td:first-child {
        padding-left: 15px;
        width: 60px;
        min-width: 60px;
        max-width: 60px;
    }
    
    /* Adjust sort arrow position */
    #usersTable th.sorting:after,
    #usersTable th.sorting_asc:after,
    #usersTable th.sorting_desc:after {
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
        $('#usersTable').DataTable({
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
                "searchPlaceholder": "Search users...",
                "lengthMenu": "Show _MENU_ entries",
                "zeroRecords": "No matching users found",
                "info": "Showing _START_ to _END_ of _TOTAL_ users",
                "infoEmpty": "No users available",
                "infoFiltered": "(filtered from _MAX_ total)"
            },
            "responsive": true
        });
    });
</script>
@endpush
@endsection
