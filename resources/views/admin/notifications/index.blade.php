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
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="notifications-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date & Time</th>
                                        <th>Need</th>
                                        <th>Retreat</th>
                                        <th>Subject</th>
                                        <th>Recipients</th>
                                        <th>Additional Emails</th>
                                        <th>Status</th>
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
                                                    <a href="{{ route('admin.retreats.show', $notification->retreat->id) }}" class="text-decoration-none">
                                                        {{ $notification->retreat->title }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($notification->subject, 40) }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $notification->total_recipients }}</span>
                                            </td>
                                            <td>
                                                @if($notification->additional_users_emails)
                                                    <small class="text-muted" title="{{ $notification->additional_users_emails }}">
                                                        {{ Str::limit($notification->additional_users_emails, 30) }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        @if(!$notifications->isEmpty())
        $('#notifications-table').DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "order": [[0, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [6, 7] }
            ]
        });
        @endif
    });
</script>
@endpush
