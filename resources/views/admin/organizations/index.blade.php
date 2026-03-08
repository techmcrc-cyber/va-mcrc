@extends('admin.layouts.app')

@section('title', 'Organizations')

@section('content')
<div class="container-fluid">
    <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
            <h4 class="m-0 fw-bold" style="color: #b53d5e; font-size: 1.5rem;">Organizations</h4>
            @if(auth()->user()->hasPermission('organizations.create'))
            <a href="{{ route('admin.organizations.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Create New Organization
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Email</th>
                            <th>Users</th>
                            <th>Retreats</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($organizations as $organization)
                        <tr>
                            <td>{{ $organization->id }}</td>
                            <td>
                                @if($organization->logo)
                                    <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" 
                                         class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-building text-white"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $organization->name }}</strong>
                                @if($organization->is_verified)
                                    <i class="fas fa-check-circle text-success ms-1" title="Verified"></i>
                                @endif
                            </td>
                            <td>
                                <code>{{ $organization->slug }}</code>
                                <a href="{{ $organization->getSubdomainUrl() }}" target="_blank" class="ms-1">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </td>
                            <td>{{ $organization->email ?? '-' }}</td>
                            <td><span class="badge bg-info">{{ $organization->users_count }}</span></td>
                            <td><span class="badge bg-primary">{{ $organization->retreats_count }}</span></td>
                            <td>
                                @if($organization->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if(auth()->user()->hasPermission('organizations.view'))
                                    <a href="{{ route('admin.organizations.show', $organization) }}" 
                                       class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endif
                                    
                                    @if(auth()->user()->hasPermission('organizations.edit'))
                                    <a href="{{ route('admin.organizations.edit', $organization) }}" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    @if(auth()->user()->hasPermission('organizations.delete'))
                                    <form action="{{ route('admin.organizations.destroy', $organization) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this organization?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No organizations found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $organizations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
