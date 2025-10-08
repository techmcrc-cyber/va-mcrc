@extends('admin.layouts.app')

@section('title', 'Edit Criteria')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Criteria</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.criteria.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Criteria
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.criteria.update', $criterion) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Criteria Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $criterion->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">Any</option>
                                    <option value="male" {{ old('gender', $criterion->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $criterion->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="min_age" class="form-label">Minimum Age</label>
                                <input type="number" class="form-control @error('min_age') is-invalid @enderror" 
                                       id="min_age" name="min_age" value="{{ old('min_age', $criterion->min_age) }}" min="0">
                                @error('min_age')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="max_age" class="form-label">Maximum Age</label>
                                <input type="number" class="form-control @error('max_age') is-invalid @enderror" 
                                       id="max_age" name="max_age" value="{{ old('max_age', $criterion->max_age) }}" min="0">
                                @error('max_age')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="married" class="form-label">Married</label>
                                <select class="form-select @error('married') is-invalid @enderror" id="married" name="married">
                                    <option value="">Any</option>
                                    <option value="yes" {{ old('married', $criterion->married) == 'yes' ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('married')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="vocation" class="form-label">Vocation</label>
                                <select class="form-select @error('vocation') is-invalid @enderror" id="vocation" name="vocation">
                                    <option value="">Any</option>
                                    <option value="priest_only" {{ old('vocation', $criterion->vocation) == 'priest_only' ? 'selected' : '' }}>Priest Only</option>
                                    <option value="sisters_only" {{ old('vocation', $criterion->vocation) == 'sisters_only' ? 'selected' : '' }}>Sisters Only</option>
                                </select>
                                @error('vocation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" 
                                       {{ old('status', $criterion->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.criteria.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Criteria
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
