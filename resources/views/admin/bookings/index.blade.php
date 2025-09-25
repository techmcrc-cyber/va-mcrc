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
                                <th style="width: 12%;">Booking ID</th>
                                <th style="width: 18%;">Retreat</th>
                                <th style="width: 20%;">Primary Guest & Contact</th>
                                <th style="width: 18%;">Dates</th>
                                <th style="width: 10%;">Participants</th>
                                <th style="width: 12%;">Status</th>
                                <th style="width: 10%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->booking_id }}</td>
                                    <td>{{ $booking->retreat->title }}</td>
                                    <td>
                                        <div class="guest-info">
                                            <strong>{{ $booking->firstname }} {{ $booking->lastname }}</strong>
                                            @if($booking->flag)
                                                <span class="badge bg-warning ml-1" data-toggle="tooltip" title="{{ $booking->flag }}">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-phone-alt"></i> {{ $booking->whatsapp_number }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope"></i> {{ $booking->email }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-info">
                                            <strong>{{ $booking->retreat->start_date->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">to</small>
                                            <br>
                                            <strong>{{ $booking->retreat->end_date->format('M d, Y') }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $booking->additional_participants + 1 }}</span>
                                        @if($booking->additional_participants > 0)
                                            <br><small class="text-muted">(+{{ $booking->additional_participants }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="status-info">
                                            @if($booking->flag)
                                                @php
                                                    $flags = explode(',', $booking->flag);
                                                @endphp
                                                @foreach($flags as $flag)
                                                    <div class="mb-1">
                                                        <span class="badge bg-warning">
                                                            {{ Str::title(str_replace('_', ' ', trim($flag))) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="badge bg-success">Confirmed</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical" role="group">
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}" 
                                               class="btn btn-sm btn-info mb-1" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.bookings.edit', $booking->id) }}" 
                                               class="btn btn-sm btn-primary mb-1" 
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
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No bookings found.</td>
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
<style>
    /* Custom styling for bookings table */
    #bookings-table {
        table-layout: fixed;
        width: 100%;
    }
    
    #bookings-table th,
    #bookings-table td {
        vertical-align: middle;
        word-wrap: break-word;
        padding: 12px 8px;
    }
    
    .guest-info {
        line-height: 1.4;
    }
    
    .guest-info strong {
        font-size: 14px;
        color: #333;
    }
    
    .guest-info small {
        font-size: 11px;
        display: block;
        margin: 2px 0;
    }
    
    .date-info {
        text-align: center;
        line-height: 1.3;
    }
    
    .date-info strong {
        font-size: 12px;
        color: #333;
    }
    
    .status-info .badge {
        font-size: 10px;
        padding: 4px 8px;
        display: inline-block;
        min-width: 70px;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 3px;
        min-width: 35px;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        #bookings-table th,
        #bookings-table td {
            padding: 8px 4px;
            font-size: 12px;
        }
        
        .guest-info strong {
            font-size: 12px;
        }
        
        .guest-info small {
            font-size: 10px;
        }
        
        .btn-group-vertical .btn {
            padding: 2px 6px;
            font-size: 11px;
        }
    }
    
    /* Badge improvements */
    .badge {
        font-weight: 500;
    }
    
    .bg-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }
    
    .bg-success {
        background-color: #28a745 !important;
        color: white;
    }
    
    .bg-primary {
        background-color: #007bff !important;
        color: white;
    }
</style>
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
