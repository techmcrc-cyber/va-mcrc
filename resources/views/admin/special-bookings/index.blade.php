@extends('admin.layouts.app')

@section('title', 'Special Bookings')

@section('content')
<div class="container-fluid">
    <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
            <h4 class="m-0 fw-bold" style="color: #b53d5e; font-size: 1.5rem;">Special Bookings (Super Admin)</h4>
            <a href="{{ route('admin.special-bookings.create') }}" class="btn btn-sm btn-warning">
                <i class="fas fa-star me-1"></i> Create Special Booking
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-warning">{{ session('warning') }}</div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="special-bookings-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Name</th>
                                    <th>WhatsApp</th>
                                    <th>Retreat</th>
                                    <th>Flags</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    #special-bookings-table th {
        font-weight: bold !important;
        background-color: #f8f9fc !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    .dataTables_length {
        margin-bottom: 15px;
    }
    
    .dataTables_length select {
        margin: 0 5px;
        padding: 4px 20px;
        border-radius: 4px;
        border: 1px solid #d1d3e2;
    }
    
    /* Action buttons styling */
    #special-bookings-table .btn-row {
        display: flex;
        gap: 4px;
        margin-bottom: 4px;
    }
    
    #special-bookings-table .btn-row:last-child {
        margin-bottom: 0;
    }
    
    #special-bookings-table .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
        line-height: 1.2;
    }
    
    #special-bookings-table .btn-sm i {
        font-size: 12px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#special-bookings-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.special-bookings.index') }}",
        columns: [
            { data: 'booking_id', name: 'booking_id' },
            { data: 'name', name: 'name' },
            { data: 'whatsapp', name: 'whatsapp', orderable: false, searchable: false },
            { data: 'retreat', name: 'retreat' },
            { data: 'flags', name: 'flags', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center', width: '120px' }
        ],
        order: [[0, 'desc']], // Order by booking_id (first column) descending
        pageLength: 25
    });
});
</script>
@endpush
