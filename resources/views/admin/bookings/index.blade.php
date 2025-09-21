@extends('admin.layouts.app')

@section('title', 'Retreat Bookings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Retreat Bookings</h3>
                    <div>
                        <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Booking
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="bookings-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Retreat</th>
                                <th>Primary Guest</th>
                                <th>Contact</th>
                                <th>Dates</th>
                                <th>Participants</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->booking_id }}</td>
                                    <td>{{ $booking->retreat->title }}</td>
                                    <td>
                                        {{ $booking->firstname }} {{ $booking->lastname }}
                                        @if($booking->flag)
                                            <span class="badge bg-warning" data-toggle="tooltip" title="{{ $booking->flag }}">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $booking->whatsapp_number }}<br>
                                        <small>{{ $booking->email }}</small>
                                    </td>
                                    <td>
                                        {{ $booking->retreat->start_date->format('M d, Y') }} - 
                                        {{ $booking->retreat->end_date->format('M d, Y') }}
                                    </td>
                                    <td>
                                        {{ $booking->additional_participants + 1 }} 
                                        <small class="text-muted">({{ $booking->additional_participants }} additional)</small>
                                    </td>
                                    <td>
                                        @if($booking->flag)
                                            <span class="badge bg-warning">
                                                {{ Str::title(str_replace('_', ' ', $booking->flag)) }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">Confirmed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.bookings.edit', $booking->id) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.bookings.destroy', $booking->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this booking? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No bookings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    {{ $bookings->links() }}
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
@endsection

@push('styles')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush

@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

<script>
    $(function () {
        $('#bookings-table').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "order": [[0, 'desc']]
        });
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
