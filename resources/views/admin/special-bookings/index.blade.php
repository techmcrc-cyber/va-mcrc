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
            { data: 'retreat', name: 'retreat' },
            { data: 'flags', name: 'flags', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[4, 'desc']],
        pageLength: 25
    });
});
</script>
@endpush
