@extends('admin.layouts.app')

@section('title', 'Email Notifications')

@section('page-title', 'Email Notifications')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Notifications</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
            <h4 class="m-0 fw-bold" style="color: #b53d5e; font-size: 1.5rem;">Email Notifications</h4>
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-envelope me-1"></i> Compose Mail
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($notifications->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No notifications sent yet</h5>
                            <p class="text-muted">Start by composing your first email notification</p>
                            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-envelope me-1"></i> Compose Mail
                            </a>
                        </div>
                    @else
                        <!-- Per Page Selector -->
                        <div class="mb-3">
                            <label for="per-page" class="me-2">Show:</label>
                            <select id="per-page" class="form-select form-select-sm d-inline-block" style="width: 80px;">
                                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="ms-2 text-muted">entries per page</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date & Time</th>
                                        <th>Need</th>
                                        <th>Retreat</th>
                                        <th>Subject</th>
                                        <th>Recipients</th>
                                        <th>Status</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notification)
                                        <tr>
                                            <td>{{ $notification->id }}</td>
                                            <td>{{ $notification->created_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $notification->need === 'retreat' ? 'info' : 'secondary' }}">
                                                    {{ ucfirst($notification->need) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($notification->retreat)
                                                    {{ Str::limit($notification->retreat->title, 30) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($notification->subject, 40) }}</td>
                                            <td class="text-center">
                                                <span class="badge fs-6 px-3 py-2" style="background-color: #4a90e2; color: white;">{{ $notification->total_recipients }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'secondary',
                                                        'queued' => 'info',
                                                        'processing' => 'warning',
                                                        'sent' => 'success',
                                                        'failed' => 'danger',
                                                        'partially_sent' => 'warning',
                                                    ];
                                                    $color = $statusColors[$notification->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">
                                                    {{ $notification->formatted_status }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal{{ $notification->id }}" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $notification->id }})" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $notification->id }}" action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $notifications->firstItem() ?? 0 }} to {{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }} entries
                            </div>
                            <div>
                                {{ $notifications->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- View Modals -->
    @if(!$notifications->isEmpty())
    @foreach($notifications as $notification)
<div class="modal fade" id="viewModal{{ $notification->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $notification->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #b53d5e; color: white;">
                <h5 class="modal-title" id="viewModalLabel{{ $notification->id }}">
                    <i class="fas fa-envelope me-2"></i>Notification Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>ID:</strong> {{ $notification->id }}
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong> 
                        @php
                            $statusColors = [
                                'pending' => 'secondary',
                                'queued' => 'info',
                                'processing' => 'warning',
                                'sent' => 'success',
                                'failed' => 'danger',
                                'partially_sent' => 'warning',
                            ];
                            $color = $statusColors[$notification->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ $notification->formatted_status }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Type:</strong> 
                        <span class="badge bg-{{ $notification->need === 'retreat' ? 'info' : 'secondary' }}">
                            {{ ucfirst($notification->need) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Created:</strong> {{ $notification->created_at->format('M d, Y h:i A') }}
                    </div>
                </div>

                @if($notification->retreat)
                <div class="mb-3">
                    <strong>Retreat:</strong> 
                    <a href="{{ route('admin.retreats.show', $notification->retreat->id) }}" target="_blank">
                        {{ $notification->retreat->title }}
                    </a>
                </div>
                @endif

                <div class="mb-3">
                    <strong>Total Recipients:</strong> 
                    <span class="badge bg-primary">{{ $notification->total_recipients }}</span>
                </div>

                @if($notification->additional_users_emails)
                <div class="mb-3">
                    <strong>Additional Emails:</strong>
                    <div class="mt-1">
                        <small class="text-muted">{{ $notification->additional_users_emails }}</small>
                    </div>
                </div>
                @endif

                @if($notification->greeting)
                <div class="mb-3">
                    <strong>Greeting:</strong>
                    <div class="mt-1 p-2 bg-light rounded">
                        <small>{{ $notification->greeting }}</small>
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <strong>Heading:</strong>
                    <div class="mt-1">
                        {{ $notification->heading }}
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Subject:</strong>
                    <div class="mt-1">
                        {{ $notification->subject }}
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Body:</strong>
                    <div class="mt-1 p-3 bg-light rounded" style="max-height: 300px; overflow-y: auto;">
                        {!! $notification->body !!}
                    </div>
                </div>

                @if($notification->sent_at)
                <div class="mb-3">
                    <strong>Sent At:</strong> {{ $notification->sent_at->format('M d, Y h:i A') }}
                </div>
                @endif

                @if($notification->creator)
                <div class="mb-3">
                    <strong>Created By:</strong> {{ $notification->creator->name }}
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif
</div>
@endsection

@push('scripts')
<script>
    // Per page selector
    $('#per-page').on('change', function() {
        const perPage = $(this).val();
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Reset to first page
        window.location.href = url.toString();
    });

    // Confirm delete
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this notification? This action cannot be undone.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
