@extends('admin.layouts.app')

@section('title', 'Create New Retreat')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 200px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Retreat</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.retreats.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">Retreat Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="whatsapp_channel_link" class="form-label">WhatsApp Channel Link *</label>
                                            <input type="url" class="form-control @error('whatsapp_channel_link') is-invalid @enderror" 
                                                   id="whatsapp_channel_link" name="whatsapp_channel_link" 
                                                   value="{{ old('whatsapp_channel_link') }}" 
                                                   placeholder="https://whatsapp.com/channel/..." required>
                                            @error('whatsapp_channel_link')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="whatsapp_template_id" class="form-label">WhatsApp Template ID</label>
                                            <input type="number" class="form-control @error('whatsapp_template_id') is-invalid @enderror" 
                                                   id="whatsapp_template_id" name="whatsapp_template_id" 
                                                   value="{{ old('whatsapp_template_id') }}" 
                                                   placeholder="1" min="1">
                                            @error('whatsapp_template_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Leave empty to use default</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <div id="editor">{!! old('description') !!}</div>
                                    <input type="hidden" name="description" id="description" value="{{ old('description') }}">
                                    @error('description')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="instructions" class="form-label">Instructions/Guidelines</label>
                                    <div id="instructions-editor">{!! old('instructions') !!}</div>
                                    <input type="hidden" name="instructions" id="instructions" value="{{ old('instructions') }}">
                                    @error('instructions')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="start_date" class="form-label">Start Date *</label>
                                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="end_date" class="form-label">End Date *</label>
                                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                                   id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="timings" class="form-label">Timings *</label>
                                            <input type="text" class="form-control @error('timings') is-invalid @enderror" 
                                                   id="timings" name="timings" value="{{ old('timings', '9:00 AM - 5:00 PM') }}" required>
                                            @error('timings')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="seats" class="form-label">Available Seats *</label>
                                            <input type="number" class="form-control @error('seats') is-invalid @enderror" 
                                                   id="seats" name="seats" value="{{ old('seats', 20) }}" min="1" required>
                                            @error('seats')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="criteria" class="form-label">Eligibility Criteria</label>
                                            <select class="form-select @error('criteria') is-invalid @enderror" 
                                                    id="criteria" name="criteria">
                                                <option value="">No Criteria</option>
                                                @foreach($criteriaOptions as $id => $name)
                                                    <option value="{{ $id }}" {{ old('criteria') == $id ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('criteria')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="special_remarks" class="form-label">Special Remarks</label>
                                            <textarea class="form-control @error('special_remarks') is-invalid @enderror" 
                                                      id="special_remarks" name="special_remarks" rows="3">{{ old('special_remarks') }}</textarea>
                                            @error('special_remarks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">Featured</label>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Create Retreat
                                            </button>
                                            <a href="{{ route('admin.retreats.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Cancel
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quill editor for description
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Enter retreat description...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image'],
                    ['clean'],
                    [{ 'color': [] }, { 'background': [] }],
                    ['blockquote', 'code-block']
                ]
            }
        });

        // Initialize Quill editor for instructions
        const instructionsQuill = new Quill('#instructions-editor', {
            theme: 'snow',
            placeholder: 'Enter instructions/guidelines...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        // Function to update hidden inputs with Quill content
        function updateHiddenInputs() {
            const descriptionInput = document.querySelector('#description');
            const instructionsInput = document.querySelector('#instructions');
            
            // Get the text content (without HTML tags) to check if it's empty
            const descriptionText = quill.getText().trim();
            const descriptionContent = quill.root.innerHTML;
            
            // Always update the hidden inputs
            descriptionInput.value = descriptionContent;
            instructionsInput.value = instructionsQuill.root.innerHTML;
            
            return descriptionText.length > 0;
        }
        
        // Update hidden inputs when the form is submitted
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            // First, update the hidden inputs
            const hasDescription = updateHiddenInputs();
            
            // Check if description is empty
            if (!hasDescription) {
                e.preventDefault();
                
                // Remove any existing error messages
                const existingError = document.querySelector('.description-error');
                if (existingError) {
                    existingError.remove();
                }
                
                // Create and show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger description-error mb-3';
                errorDiv.textContent = 'The description field is required.';
                
                // Insert error message after the description field
                const descriptionContainer = document.querySelector('.form-group:has(#editor)');
                descriptionContainer.appendChild(errorDiv);
                
                // Scroll to the error
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                return false;
            }
            
            // If we get here, the form is valid and will be submitted
        });
        
        // Also update hidden inputs when the editor loses focus
        quill.on('text-change', updateHiddenInputs);
        instructionsQuill.on('text-change', updateHiddenInputs);

        // Set minimum end date based on start date
        const startDateInput = document.querySelector('#start_date');
        const endDateInput = document.querySelector('#end_date');
        
        function validateDates() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            
            // Remove any existing custom error messages
            const existingError = endDateInput.parentNode.querySelector('.date-error');
            if (existingError) {
                existingError.remove();
            }
            endDateInput.classList.remove('is-invalid');
            
            if (startDateInput.value && endDateInput.value && endDate <= startDate) {
                // Add error styling
                endDateInput.classList.add('is-invalid');
                
                // Create error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback date-error';
                errorDiv.textContent = 'The end date must be after the start date.';
                endDateInput.parentNode.appendChild(errorDiv);
                
                return false;
            }
            return true;
        }
        
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if (new Date(endDateInput.value) < new Date(this.value)) {
                endDateInput.value = this.value;
            }
            validateDates();
        });
        
        endDateInput.addEventListener('change', function() {
            validateDates();
        });
        
        // Enhanced form validation
        form.addEventListener('submit', function(e) {
            if (!validateDates()) {
                e.preventDefault();
                endDateInput.focus();
                return false;
            }
        });
    });
</script>
@endpush
