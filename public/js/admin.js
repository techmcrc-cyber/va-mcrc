/**
 * Admin Panel JavaScript
 * Handles all the interactive elements of the admin panel
 */

document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('sidebar-wrapper').classList.toggle('toggled');
            
            // Adjust navbar position when sidebar is toggled on mobile
            const navbar = document.querySelector('.navbar');
            if (window.innerWidth < 992) {
                if (document.getElementById('sidebar-wrapper').classList.contains('toggled')) {
                    navbar.style.left = '250px';
                } else {
                    navbar.style.left = '0';
                }
            }
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(dropdown => {
            if (!dropdown.previousElementSibling.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds (except those with no-auto-hide class)
    const alerts = document.querySelectorAll('.alert:not(.no-auto-hide)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Toggle password visibility
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });

    // Handle file input change
    const fileInputs = document.querySelectorAll('.custom-file-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Choose file';
            const label = this.nextElementSibling;
            label.textContent = fileName;
        });
    });

    // Initialize DataTables
    if (typeof $.fn.DataTable === 'function') {
        $('.datatable').DataTable({
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search...",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries found",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "responsive": true,
            "autoWidth": false,
            "order": [],
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
        });
    }

    // Handle bulk actions
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Handle bulk action form submission
    const bulkActionForm = document.getElementById('bulkActionForm');
    if (bulkActionForm) {
        bulkActionForm.addEventListener('submit', function(e) {
            const selectedItems = document.querySelectorAll('.item-checkbox:checked');
            if (selectedItems.length === 0) {
                e.preventDefault();
                alert('Please select at least one item to perform this action.');
                return false;
            }
            
            const action = document.getElementById('bulkAction').value;
            if (action === 'delete') {
                if (!confirm('Are you sure you want to delete the selected items? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
            }
            return true;
        });
    }

    // Handle form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Handle image preview
    const imageInput = document.getElementById('image');
    if (imageInput) {
        const imagePreview = document.getElementById('imagePreview');
        const defaultImage = imagePreview ? imagePreview.src : '';
        
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (imagePreview) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                }
                reader.readAsDataURL(file);
            } else {
                if (imagePreview) {
                    imagePreview.src = defaultImage;
                }
            }
        });
    }

    // Handle rich text editor initialization
    if (typeof ClassicEditor !== 'undefined') {
        document.querySelectorAll('.rich-editor').forEach(node => {
            ClassicEditor
                .create(node, {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
                })
                .catch(error => {
                    console.error(error);
                });
        });
    }

    // Handle date picker initialization
    if (typeof flatpickr !== 'undefined') {
        flatpickr("[data-datepicker]", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
        
        flatpickr("[data-datetimepicker]", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            allowInput: true
        });
    }

    // Handle select2 initialization
    if (typeof $().select2 === 'function') {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // Handle sidebar active state
    const currentUrl = window.location.href.toLowerCase();
    const menuItems = document.querySelectorAll('.list-group-item');
    menuItems.forEach(item => {
        const href = item.getAttribute('href') || '';
        if (currentUrl.includes(href.toLowerCase()) && href !== '') {
            item.classList.add('active');
            // Expand parent menu if it's a dropdown item
            const parentMenu = item.closest('.collapse');
            if (parentMenu) {
                parentMenu.classList.add('show');
                const parentLink = parentMenu.previousElementSibling;
                if (parentLink) {
                    parentLink.setAttribute('aria-expanded', 'true');
                }
            }
        }
    });

    // Handle sweetalert2 confirmations
    window.confirmAction = function(e, message = 'Are you sure you want to perform this action?') {
        e.preventDefault();
        const form = e.target.closest('form');
        
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                if (form) {
                    form.submit();
                } else if (e.target.href) {
                    window.location.href = e.target.href;
                }
            }
        });
    };
});

// Helper function to show loading state
function showLoading(button, text = 'Processing...') {
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        <span class="visually-hidden">${text}</span>
    `;
    return originalText;
}

// Helper function to reset button state
function resetButton(button, originalText) {
    button.disabled = false;
    button.innerHTML = originalText;
}

// Handle AJAX form submissions
function submitForm(form, successCallback = null, errorCallback = null) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton ? showLoading(submitButton) : null;
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (submitButton) resetButton(submitButton, originalText);
        if (data.redirect) {
            window.location.href = data.redirect;
        } else if (successCallback) {
            successCallback(data);
        } else {
            // Default success handling
            Swal.fire({
                title: 'Success!',
                text: data.message || 'Operation completed successfully.',
                icon: 'success'
            }).then(() => {
                if (data.reload) window.location.reload();
            });
        }
    })
    .catch(error => {
        if (submitButton) resetButton(submitButton, originalText);
        if (errorCallback) {
            errorCallback(error);
        } else {
            // Default error handling
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred. Please try again.',
                icon: 'error'
            });
        }
        console.error('Error:', error);
    });
    
    return false;
}

// Initialize tooltips on dynamically added elements
$(document).on('mouseover', '[data-bs-toggle="tooltip"]', function() {
    const tooltip = new bootstrap.Tooltip(this);
    tooltip.show();
});

// Handle file preview
function previewFile(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    const reader = new FileReader();

    reader.onloadend = function() {
        if (preview) {
            preview.src = reader.result;
            preview.style.display = 'block';
        }
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        if (preview) {
            preview.src = '';
            preview.style.display = 'none';
        }
    }
}
