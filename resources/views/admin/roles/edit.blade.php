@extends('admin.layouts.app')

@section('title', 'Edit Role: ' . $role->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if($role->is_super_admin)
                <div class="alert alert-warning">
                    <strong>Note:</strong> This is a super admin role. Some restrictions may apply.
                </div>
            @endif
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Role: {{ $role->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Roles
                        </a>
                    </div>
                </div>
                <div class="card-body">
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $role->name) }}" 
                                   {{ $role->is_super_admin ? 'readonly' : 'required' }}>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                   id="description" name="description" value="{{ old('description', $role->description) }}"
                                   {{ $role->is_super_admin ? 'readonly' : '' }}>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', $role->is_active) ? 'checked' : '' }}
                           {{ $role->is_super_admin ? 'disabled' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                    @if($role->is_super_admin)
                        <input type="hidden" name="is_active" value="1">
                    @endif
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Permissions</h6>
                    </div>
                    <div class="card-body">
                        @error('permissions')
                            <div class="alert alert-danger">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror

                        @foreach($permissions as $module => $modulePermissions)
                            <div class="permission-module mb-4">
                                <h6 class="font-weight-bold">{{ $module }}</h6>
                                <div class="row">
                                    @foreach($modulePermissions as $permission)
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $permission['id'] }}"
                                                       id="permission-{{ $permission['id'] }}"
                                                       {{ in_array($permission['id'], old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                                                       {{ $role->is_super_admin ? 'disabled' : '' }}>
                                                <label class="form-check-label" for="permission-{{ $permission['id'] }}">
                                                    {{ $permission['name'] }}
                                                </label>
                                                @if($permission['description'])
                                                    <small class="d-block text-muted">{{ $permission['description'] }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                        
                        @if($role->is_super_admin)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Super admin roles have all permissions by default.
                            </div>
                            @foreach($permissions as $modulePermissions)
                            @foreach($modulePermissions as $permission)
                                <input type="hidden" name="permissions[]" value="{{ $permission['id'] }}">
                            @endforeach
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" {{ $role->is_super_admin ? 'disabled' : '' }}>
                        <i class="fas fa-save"></i> Update Role
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .permission-module {
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    .form-check {
        padding-left: 1.5rem;
    }
</style>
@endpush
