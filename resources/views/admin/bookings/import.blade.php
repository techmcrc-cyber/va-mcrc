@extends('admin.layouts.app')

@section('title', 'Import Bookings')

@push('styles')
<style>
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 0.375rem;
        padding: 3rem;
        text-align: center;
        transition: border-color 0.3s ease;
        background-color: #f8f9fa;
    }
    .upload-area:hover {
        border-color: #007bff;
        background-color: #e3f2fd;
    }
    .upload-area.dragover {
        border-color: #007bff;
        background-color: #e3f2fd;
    }
    .file-icon {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    .upload-area.has-file {
        border-color: #28a745;
        background-color: #d4edda;
    }
    .upload-area.has-file .file-icon {
        color: #28a745;
    }
    .instruction-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
    }
    .step-number {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-import me-2"></i>Import Bookings
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Bookings
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Instructions Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="instruction-card">
                                <h5 class="mb-3">
                                    <i class="fas fa-info-circle me-2"></i>How to Import Bookings
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="step-number">1</div>
                                            <span>Download the import template</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="step-number">2</div>
                                            <span>Fill in the participant details</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="step-number">3</div>
                                            <span>Upload and review before import</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Download Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info no-auto-hide">
                                <h6 class="alert-heading">
                                    <i class="fas fa-download me-2"></i>Step 1: Download Import Template
                                </h6>
                                <p class="mb-2">Download the Excel template with sample data. <strong>Family/group bookings made easy</strong> - use Group ID to link participants together.</p>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <h6 class="text-primary"><i class="fas fa-users me-1"></i>Group Bookings:</h6>
                                        <ul class="small mb-0">
                                            <li><strong>Group ID</strong>: Same number for family members</li>
                                            <li>Example: 1,1,1 for family of 3</li>
                                            <li>First row = Primary participant</li>
                                            <li>Max {{ config('bookings.max_additional_members', 3) + 1 }} people per group</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-success"><i class="fas fa-check me-1"></i>Required Fields:</h6>
                                        <ul class="small mb-0">
                                            <li>Personal: Name, Age, Gender</li>
                                            <li>Address: Full address, City, State</li>
                                            <li>Emergency Contact: Name & Phone</li>
                                            <li>Email/Phone: Optional for minors</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-info"><i class="fas fa-magic me-1"></i>Auto-Generated:</h6>
                                        <ul class="small mb-0">
                                            <li>Booking ID (unique per group)</li>
                                            <li>Retreat details (from selection)</li>
                                            <li>Participant numbering</li>
                                            <li>Validation flags & timestamps</li>
                                        </ul>
                                    </div>
                                </div>
                                <a href="{{ route('admin.bookings.import.template') }}" class="btn btn-info">
                                    <i class="fas fa-download me-2"></i>Download Template
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Import Form -->
                    <form action="{{ route('admin.bookings.import.preview') }}" method="POST" enctype="multipart/form-data" id="import-form">
                        @csrf
                        
                        <div class="row">
                            <!-- Retreat Selection -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-calendar-alt me-2 text-primary"></i>Step 2: Select Retreat
                                        </h6>
                                        <div class="form-group">
                                            <label for="retreat_id" class="form-label">Choose Target Retreat <span class="text-danger">*</span></label>
                                            <select name="retreat_id" id="retreat_id" class="form-select @error('retreat_id') is-invalid @enderror" required>
                                                <option value="">-- Select Retreat --</option>
                                                @foreach($retreats as $retreat)
                                                    <option value="{{ $retreat->id }}" 
                                                            data-criteria="{{ $retreat->criteria }}"
                                                            {{ old('retreat_id') == $retreat->id ? 'selected' : '' }}>
                                                        {{ $retreat->title }} 
                                                        ({{ $retreat->start_date->format('M d, Y') }} - {{ $retreat->end_date->format('M d, Y') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('retreat_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small id="retreat-criteria" class="form-text text-muted"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- File Upload -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-file-upload me-2 text-success"></i>Step 3: Upload File
                                        </h6>
                                        <div class="upload-area" id="upload-area">
                                            <div class="file-icon">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                            </div>
                                            <h6>Drag & Drop your file here</h6>
                                            <p class="text-muted mb-3">or click to browse</p>
                                            <input type="file" name="import_file" id="import_file" 
                                                   class="form-control d-none @error('import_file') is-invalid @enderror" 
                                                   accept=".xlsx,.xls,.csv" required>
                                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('import_file').click()">
                                                <i class="fas fa-folder-open me-2"></i>Browse Files
                                            </button>
                                            <div class="mt-2">
                                                <small class="text-muted">Supported formats: Excel (.xlsx, .xls) and CSV (.csv)</small><br>
                                                <small class="text-muted">Maximum file size: 10MB</small>
                                            </div>
                                            @error('import_file')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div id="file-info" class="mt-3 d-none">
                                            <div class="alert alert-success">
                                                <i class="fas fa-file-excel me-2"></i>
                                                <span id="file-name"></span>
                                                <button type="button" class="btn btn-sm btn-outline-danger float-end" onclick="clearFile()">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Import Options -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-cog me-2 text-warning"></i>Import Options
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1" checked>
                                                    <label class="form-check-label" for="skip_duplicates">
                                                        Skip Duplicate Entries
                                                    </label>
                                                    <small class="form-text text-muted d-block">
                                                        Skip participants who have booked in the past year
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="validate_criteria" name="validate_criteria" value="1" checked>
                                                    <label class="form-check-label" for="validate_criteria">
                                                        Validate Retreat Criteria
                                                    </label>
                                                    <small class="form-text text-muted d-block">
                                                        Check if participants meet retreat eligibility
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" id="preview-btn" disabled>
                                        <i class="fas fa-eye me-2"></i>Preview Import
                                    </button>
                                    <small class="text-muted text-center">
                                        You'll be able to review all data before the actual import
                                    </small>
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
<script>
$(document).ready(function() {
    // Show retreat criteria when retreat is selected
    $('#retreat_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var criteria = selectedOption.data('criteria');
        var criteriaMap = {
            'male_only': 'Only for Males',
            'female_only': 'Only for Females', 
            'priests_only': 'Only for Priests (Congregation field required)',
            'sisters_only': 'Only for Sisters (Female + Congregation field required)',
            'youth_only': 'Only for Youth (Age 16-30)',
            'children': 'Only for Children (Age 15 or below)',
            'no_criteria': 'Open to All'
        };
        
        if (criteria) {
            $('#retreat-criteria').html('<strong>Eligibility:</strong> ' + (criteriaMap[criteria] || 'Not specified'));
            if (criteria === 'priests_only' || criteria === 'sisters_only') {
                $('#retreat-criteria').addClass('text-warning');
            } else {
                $('#retreat-criteria').removeClass('text-warning').addClass('text-info');
            }
        } else {
            $('#retreat-criteria').text('');
        }
        
        checkFormValidity();
    });

    // File upload handling
    var uploadArea = $('#upload-area');
    var fileInput = $('#import_file');

    // Click to upload
    uploadArea.on('click', function() {
        fileInput.click();
    });

    // Drag and drop
    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });

    uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });

    uploadArea.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            fileInput[0].files = files;
            handleFileSelect(files[0]);
        }
    });

    // File input change
    fileInput.on('change', function() {
        if (this.files.length > 0) {
            handleFileSelect(this.files[0]);
        }
    });

    function handleFileSelect(file) {
        // Validate file type
        var allowedTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'];
        var allowedExtensions = ['.xlsx', '.xls', '.csv'];
        var fileName = file.name.toLowerCase();
        var validExtension = allowedExtensions.some(ext => fileName.endsWith(ext));
        
        if (!validExtension) {
            alert('Please select a valid Excel or CSV file.');
            clearFile();
            return;
        }

        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB.');
            clearFile();
            return;
        }

        // Show file info
        uploadArea.addClass('has-file');
        $('#file-name').text(file.name + ' (' + formatFileSize(file.size) + ')');
        $('#file-info').removeClass('d-none');
        
        checkFormValidity();
    }

    function clearFile() {
        fileInput.val('');
        uploadArea.removeClass('has-file');
        $('#file-info').addClass('d-none');
        checkFormValidity();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function checkFormValidity() {
        var retreatSelected = $('#retreat_id').val() !== '';
        var fileSelected = fileInput[0].files.length > 0;
        
        $('#preview-btn').prop('disabled', !(retreatSelected && fileSelected));
    }

    // Form submission
    $('#import-form').on('submit', function() {
        $('#preview-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
    });

    // Make clearFile function global
    window.clearFile = clearFile;
});
</script>
@endpush