@extends('admin.layouts.app')

@section('title', 'Compose Email Notification')

@section('page-title', 'Compose Email Notification')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}">Notifications</a></li>
    <li class="breadcrumb-item active">Compose</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-header" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
                    <h4 class="m-0 fw-bold" style="color: #b53d5e;">Compose Email Notification</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.notifications.store') }}" method="POST" id="notification-form">
                        @csrf

                        <!-- Notification Type -->
                        <div class="mb-3">
                            <label for="need" class="form-label">Notification Type <span class="text-danger">*</span></label>
                            <select name="need" id="need" class="form-select @error('need') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="retreat" {{ old('need') === 'retreat' ? 'selected' : '' }}>Retreat Participants</option>
                                <option value="custom" {{ old('need') === 'custom' ? 'selected' : '' }}>Custom Email List</option>
                            </select>
                            @error('need')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Choose whether to send to retreat participants or a custom email list</small>
                        </div>

                        <!-- Retreat Selection (shown when need=retreat) -->
                        <div class="mb-3" id="retreat-field" style="display: none;">
                            <label for="retreat_id" class="form-label">Select Retreat <span class="text-danger">*</span></label>
                            <select name="retreat_id" id="retreat_id" class="form-select @error('retreat_id') is-invalid @enderror">
                                <option value="">Select Retreat</option>
                                @foreach($activeRetreats as $retreat)
                                    <option value="{{ $retreat->id }}" {{ old('retreat_id') == $retreat->id ? 'selected' : '' }}>
                                        {{ $retreat->title }} ({{ $retreat->start_date->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('retreat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Select the retreat whose participants will receive this notification</small>
                        </div>

                        <!-- Additional Emails -->
                        <div class="mb-3">
                            <label for="additional_users_emails" class="form-label">
                                Additional Email Addresses
                                <span class="text-danger" id="additional-required" style="display: none;">*</span>
                            </label>
                            <textarea name="additional_users_emails" id="additional_users_emails" 
                                      class="form-control @error('additional_users_emails') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="email1@example.com, email2@example.com, email3@example.com">{{ old('additional_users_emails') }}</textarea>
                            @error('additional_users_emails')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Enter email addresses separated by commas</small>
                        </div>

                        <hr class="my-4">

                        <!-- Heading -->
                        <div class="mb-3">
                            <label for="heading" class="form-label">Email Heading <span class="text-danger">*</span></label>
                            <input type="text" name="heading" id="heading" 
                                   class="form-control @error('heading') is-invalid @enderror" 
                                   value="{{ old('heading') }}" 
                                   placeholder="e.g., Important Update" 
                                   required>
                            @error('heading')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">This will appear as the main heading in the email</small>
                        </div>

                        <!-- Subject -->
                        <div class="mb-3">
                            <label for="subject" class="form-label">Email Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="subject" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   value="{{ old('subject') }}" 
                                   placeholder="e.g., Retreat Schedule Update" 
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">This will appear in the recipient's inbox</small>
                        </div>

                        <!-- Body -->
                        <div class="mb-3">
                            <label for="body" class="form-label">Email Body <span class="text-danger">*</span></label>
                            <textarea name="body" id="body" 
                                      class="form-control @error('body') is-invalid @enderror" 
                                      rows="10">{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Enter the main content of your email</small>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-paper-plane me-1"></i> Send Notification
                            </button>
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
    let editor;

    $(document).ready(function() {
        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#body'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
            })
            .then(newEditor => {
                editor = newEditor;
            })
            .catch(error => {
                console.error(error);
            });

        // Handle notification type change
        $('#need').on('change', function() {
            const needType = $(this).val();
            
            if (needType === 'retreat') {
                $('#retreat-field').show();
                $('#retreat_id').prop('required', true);
                $('#additional-required').hide();
                $('#additional_users_emails').prop('required', false);
            } else if (needType === 'custom') {
                $('#retreat-field').hide();
                $('#retreat_id').prop('required', false);
                $('#retreat_id').val('');
                $('#additional-required').show();
                $('#additional_users_emails').prop('required', true);
            } else {
                $('#retreat-field').hide();
                $('#retreat_id').prop('required', false);
                $('#additional-required').hide();
                $('#additional_users_emails').prop('required', false);
            }
        });

        // Trigger change on page load if value exists
        if ($('#need').val()) {
            $('#need').trigger('change');
        }

        // Form submission with loading state and CKEditor validation
        $('#notification-form').on('submit', function(e) {
            // Update CKEditor data to textarea
            if (editor) {
                const editorData = editor.getData();
                $('#body').val(editorData);
                
                // Validate body is not empty
                if (!editorData.trim()) {
                    e.preventDefault();
                    alert('Please enter the email body content.');
                    return false;
                }
            }
            
            $('#submit-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Sending...');
        });
    });
</script>
@endpush
