@extends('admin.layouts.app')

@section('title', 'Organization Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Organization Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.organizations.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Organizations
                        </a>
                        @if(auth()->user()->hasPermission('edit-organizations'))
                        <a href="{{ route('admin.organizations.edit', $organization) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            @if($organization->logo)
                                <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" 
                                     class="img-thumbnail mb-3" style="max-width: 200px;">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width: 200px; height: 200px;">
                                    <i class="fas fa-building fa-5x text-white"></i>
                                </div>
                            @endif
                            
                            <div class="mb-2">
                                @if($organization->is_verified)
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Verified</span>
                                @else
                                    <span class="badge bg-warning"><i class="fas fa-clock"></i> Not Verified</span>
                                @endif
                            </div>
                            
                            <div>
                                @if($organization->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-9">
                            <h4 class="mb-3">{{ $organization->name }}</h4>
                            
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Subdomain</th>
                                    <td>
                                        <code>{{ $organization->slug }}</code>
                                        <a href="{{ $organization->getSubdomainUrl() }}" target="_blank" class="ms-2">
                                            {{ $organization->getSubdomainUrl() }} <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $organization->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $organization->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $organization->address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $organization->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Created</th>
                                    <td>{{ $organization->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $organization->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-users"></i> Users</h5>
                                    <h2>{{ $organization->users->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-calendar-alt"></i> Retreats</h5>
                                    <h2>{{ $organization->retreats->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-ticket-alt"></i> Bookings</h5>
                                    <h2>{{ $organization->bookings->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Recent Users</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($organization->users->take(5) as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role->name ?? '-' }}</td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No users found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mb-3 mt-4">Leadership Team</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($organization->leaders as $leader)
                                <tr>
                                    <td>{{ $leader->name }}</td>
                                    <td>{{ $leader->title }}</td>
                                    <td>
                                        @if($leader->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No leaders found</td>
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
