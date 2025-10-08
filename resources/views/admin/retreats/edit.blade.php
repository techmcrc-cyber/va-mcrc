@extends('admin.layouts.app')

@section('title', 'Edit Retreat: ' . $retreat->title)

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
                    <h3 class="card-title">Edit Retreat: {{ $retreat->title }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.retreats.update', $retreat) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">Retreat Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $retreat->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="whatsapp_channel_link" class="form-label">WhatsApp Channel Link *</label>
                                    <input type="url" class="form-control @error('whatsapp_channel_link') is-invalid @enderror" 
                                           id="whatsapp_channel_link" name="whatsapp_channel_link" 
                                           value="{{ old('whatsapp_channel_link', $retreat->whatsapp_channel_link) }}" 
                                           placeholder="https://whatsapp.com/channel/..." required>
                                    @error('whatsapp_channel_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <div id="editor">{!! old('description', $retreat->description) !!}</div>
                                    <input type="hidden" name="description" id="description" value="{{ old('description', $retreat->description) }}">
                                    @error('description')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="instructions" class="form-label">Instructions/Guidelines</label>
                                    <div id="instructions-editor">{!! old('instructions', $retreat->instructions) !!}</div>
                                    <input type="hidden" name="instructions" id="instructions" value="{{ old('instructions', $retreat->instructions) }}">
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
                                                   id="start_date" name="start_date" 
                                                   value="{{ old('start_date', $retreat->start_date->format('Y-m-d')) }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="end_date" class="form-label">End Date *</label>
                                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                                   id="end_date" name="end_date" 
                                                   value="{{ old('end_date', $retreat->end_date->format('Y-m-d')) }}" required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="timings" class="form-label">Timings *</label>
                                            <input type="text" class="form-control @error('timings') is-invalid @enderror" 
                                                   id="timings" name="timings" 
                                                   value="{{ old('timings', $retreat->timings) }}" required>
                                            @error('timings')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="seats" class="form-label">Available Seats *</label>
                                            <input type="number" class="form-control @error('seats') is-invalid @enderror" 
                                                   id="seats" name="seats" 
                                                   value="{{ old('seats', $retreat->seats) }}" min="1" required>
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
                                                    <option value="{{ $id }}" {{ old('criteria', $retreat->criteria) == $id ? 'selected' : '' }}>
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
                                                      id="special_remarks" name="special_remarks" rows="3">{{ old('special_remarks', $retreat->special_remarks) }}</textarea>
                                            @error('special_remarks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_active" 
                                                   name="is_active" value="1" {{ old('is_active', $retreat->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_featured" 
                                                   name="is_featured" value="1" {{ old('is_featured', $retreat->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">Featured</label>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Retreat
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

        // Set initial content for Quill editors
        const descriptionContent = document.querySelector('#description').value;
        const instructionsContent = document.querySelector('#instructions').value;
        
        if (descriptionContent) {
            quill.clipboard.dangerouslyPasteHTML(descriptionContent);
        }
        
        if (instructionsContent) {
            instructionsQuill.clipboard.dangerouslyPasteHTML(instructionsContent);
        }

        // Update hidden input on form submit
        const form = document.querySelector('form');
        form.onsubmit = function() {
            const description = document.querySelector('#description');
            const instructions = document.querySelector('#instructions');
            description.value = quill.root.innerHTML;
            instructions.value = instructionsQuill.root.innerHTML;
        };

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
