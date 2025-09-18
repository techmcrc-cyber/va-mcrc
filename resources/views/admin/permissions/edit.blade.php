@extends('admin.layouts.app')

@section('title', 'Edit Permission: ' . $permission->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Permission: {{ $permission->name }}</h1>
        <div>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Permissions
            </a>
            @if($permission->is_deletable)
                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Are you sure you want to delete this permission? This action cannot be undone.')">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                               value="{{ old('name', $permission->name) }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" 
                               value="{{ old('slug', $permission->slug) }}" required>
                        <small class="form-text text-muted">Lowercase, words separated by hyphens (e.g., create-users)</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="module" class="form-label">Module <span class="text-danger">*</span></label>
                        <select class="form-select @error('module') is-invalid @enderror" id="module" name="module" required>
                            <option value="" disabled>Select Module</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}" 
                                    {{ old('module', $permission->module) == $module ? 'selected' : '' }}>
                                    {{ ucfirst($module) }}
                                </option>
                            @endforeach
                        </select>
                        @error('module')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $permission->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" 
                              name="description" rows="3">{{ old('description', $permission->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary me-md-2">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const slug = name.toLowerCase()
            .replace(/[^\w\s-]/g, '') // Remove special characters
            .replace(/\s+/g, '-')       // Replace spaces with -
            .replace(/-+/g, '-')        // Replace multiple - with single -
            .replace(/^-+|-+$/g, '');   // Remove leading/trailing -
        
        document.getElementById('slug').value = slug;
    });
</script>
@endpush
@endsection
