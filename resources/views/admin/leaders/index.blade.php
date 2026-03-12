@extends('admin.layouts.app')

@section('title', 'Leaders')

@section('content')
<div class="container-fluid">
    <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
            <h4 class="m-0 fw-bold" style="color: #b53d5e; font-size: 1.5rem;">Leadership Team</h4>
            @if(auth()->user()->hasPermission('create-leaders'))
            <a href="{{ route('admin.leaders.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Add New Leader
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

    @if(auth()->user()->isSuperAdmin() && $organizations->isNotEmpty())
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.leaders.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="organization_id" class="form-label">Filter by Organization</label>
                        <select name="organization_id" id="organization_id" class="form-select">
                            <option value="">All Organizations</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">Order</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Title</th>
                            @if(auth()->user()->isSuperAdmin())
                            <th>Organization</th>
                            @endif
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaders as $leader)
                        <tr>
                            <td class="text-center">{{ $leader->display_order }}</td>
                            <td>
                                @if($leader->image)
                                    <img src="{{ asset('storage/' . $leader->image) }}" alt="{{ $leader->name }}" 
                                         class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                            </td>
                            <td><strong>{{ $leader->name }}</strong></td>
                            <td>{{ $leader->title }}</td>
                            @if(auth()->user()->isSuperAdmin())
                            <td>{{ $leader->organization->name ?? '-' }}</td>
                            @endif
                            <td>
                                @if($leader->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if(auth()->user()->hasPermission('view-leaders'))
                                    <a href="{{ route('admin.leaders.show', $leader) }}" 
                                       class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endif
                                    
                                    @if(auth()->user()->hasPermission('edit-leaders'))
                                    <a href="{{ route('admin.leaders.edit', $leader) }}" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    @if(auth()->user()->hasPermission('delete-leaders'))
                                    <form action="{{ route('admin.leaders.destroy', $leader) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this leader?');">
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
                            <td colspan="{{ auth()->user()->isSuperAdmin() ? '7' : '6' }}" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No leaders found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $leaders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
