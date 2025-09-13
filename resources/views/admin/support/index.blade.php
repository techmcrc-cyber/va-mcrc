@extends('admin.layouts.app')

@section('page-title', 'Support Center')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Support Center</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <h5 class="mb-4">Submit a Support Ticket</h5>
                        
                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('admin.support.submit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Your Name</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ old('name', auth()->user()->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="{{ old('email', auth()->user()->email) }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Select a category</option>
                                            @foreach($categories as $key => $label)
                                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Priority</label>
                                        <select class="form-select" id="priority" name="priority" required>
                                            @foreach($priorities as $key => $label)
                                                <option value="{{ $key }}" {{ old('priority') == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="{{ old('subject') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="attachments" class="form-label">Attachments (Optional)</label>
                                <input class="form-control" type="file" id="attachments" name="attachments[]" multiple>
                                <div class="form-text">You can upload up to 5 files (5MB each). Allowed types: JPG, PNG, PDF, DOC, DOCX, TXT</div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Ticket
                                </button>
                                <a href="#" class="text-muted small">View existing tickets</a>
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-lg-4 mt-5 mt-lg-0">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="fas fa-info-circle me-2 text-primary"></i> Support Information</h6>
                                
                                <div class="mb-4">
                                    <p class="mb-2">Having trouble? Our support team is here to help you with any questions or issues you might have.</p>
                                    <p class="mb-0">We typically respond within 24-48 hours.</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="mb-3">Contact Options</h6>
                                    <p class="mb-2">
                                        <i class="fas fa-envelope me-2 text-muted"></i>
                                        <a href="mailto:support@example.com" class="text-decoration-none">support@example.com</a>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-phone me-2 text-muted"></i>
                                        +1 (555) 123-4567
                                    </p>
                                </div>
                                
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-lightbulb me-2"></i> Before You Submit</h6>
                                    <ul class="mb-0 ps-3">
                                        <li>Check our <a href="#" class="alert-link">Help Center</a> for quick answers</li>
                                        <li>Include relevant screenshots if reporting an issue</li>
                                        <li>Be as detailed as possible in your description</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-4">Frequently Asked Questions</h5>
                
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 mb-2">
                        <h6 class="accordion-header" id="headingOne">
                            <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                How long does it take to get a response?
                            </button>
                        </h6>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Our support team typically responds within 24-48 hours. For urgent matters, please indicate "High" priority when submitting your ticket.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 mb-2">
                        <h6 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                What information should I include in my support request?
                            </button>
                        </h6>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Please include as much detail as possible about your issue, including steps to reproduce, any error messages, and relevant screenshots. The more information you provide, the faster we can assist you.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0">
                        <h6 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                How can I check the status of my support ticket?
                            </button>
                        </h6>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Once you submit a ticket, you'll receive a confirmation email with a ticket number. You can reply to that email to add more information or check the status. We'll also notify you via email when there are updates to your ticket.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
