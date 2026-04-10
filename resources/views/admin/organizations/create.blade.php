@extends('admin.layouts.app')

@section('title', 'Create New Organization')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Organization</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.organizations.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Organizations
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.organizations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <h5 class="mb-3 text-primary"><i class="fas fa-building me-2"></i>Organization Details</h5>
                        <hr>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Organization Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="organization_url" class="form-label">Organization URL <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ rtrim(config('app.url'), '/') }}/</span>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="organization_url" name="slug" value="{{ old('slug') }}" required>
                                </div>
                                <small class="form-text text-muted">Only lowercase letters, numbers, and hyphens</small>
                                @error('slug')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="2">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            <input class="form-control @error('logo') is-invalid @enderror" 
                                   type="file" id="logo" name="logo" accept="image/*">
                            <small class="form-text text-muted">Max size: 2MB. Recommended: 120x100px</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Verified</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_verified" 
                                           name="is_verified" value="1" {{ old('is_verified', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_verified">Organization is verified</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" 
                                           name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3 mt-4 text-primary"><i class="fas fa-user-shield me-2"></i>Organization Owner</h5>
                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="owner_name" class="form-label">Owner Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('owner_name') is-invalid @enderror" 
                                       id="owner_name" name="owner_name" value="{{ old('owner_name') }}" required>
                                @error('owner_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="owner_email" class="form-label">Owner Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('owner_email') is-invalid @enderror" 
                                       id="owner_email" name="owner_email" value="{{ old('owner_email') }}" required>
                                @error('owner_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="owner_password" class="form-label">Owner Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('owner_password') is-invalid @enderror" 
                                       id="owner_password" name="owner_password" required>
                                <small class="form-text text-muted">Minimum 8 characters</small>
                                @error('owner_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="owner_password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" 
                                       id="owner_password_confirmation" name="owner_password_confirmation" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="owner_phone" class="form-label">Owner Phone</label>
                            <input type="text" class="form-control @error('owner_phone') is-invalid @enderror" 
                                   id="owner_phone" name="owner_phone" value="{{ old('owner_phone') }}">
                            @error('owner_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="reset" class="btn btn-secondary me-md-2">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Create Organization
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('organization_url').value = slug;
    });
</script>
@endpush
@endsection
