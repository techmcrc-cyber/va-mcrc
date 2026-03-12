@extends('admin.layouts.app')

@section('title', 'Leader Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Leader Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.leaders.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Leaders
                        </a>
                        @if(auth()->user()->hasPermission('edit-leaders'))
                        <a href="{{ route('admin.leaders.edit', $leader) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            @if($leader->image)
                                <img src="{{ asset('storage/' . $leader->image) }}" alt="{{ $leader->name }}" 
                                     class="rounded-circle mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width: 200px; height: 200px;">
                                    <i class="fas fa-user fa-5x text-white"></i>
                                </div>
                            @endif
                            
                            <div>
                                @if($leader->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-9">
                            <h4 class="mb-1">{{ $leader->name }}</h4>
                            <p class="text-muted mb-3">{{ $leader->title }}</p>
                            
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Organization</th>
                                    <td>{{ $leader->organization->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $leader->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Display Order</th>
                                    <td>{{ $leader->display_order }}</td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <td>{{ $leader->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Created</th>
                                    <td>{{ $leader->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $leader->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @if($leader->updater)
                                <tr>
                                    <th>Updated By</th>
                                    <td>{{ $leader->updater->name }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
