@extends('admin.layouts.app')

@section('page-title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
<!-- Welcome Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="welcome-card bg-gradient-primary text-white rounded-3 p-4 p-md-5 position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 me-4 mt-3 d-none d-md-block">
                <i class="fas fa-cross fa-4x opacity-25"></i>
            </div>
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-3">Welcome back, {{ Auth::user()->name }}! <span class="wave">👋</span></h2>
                    <p class="lead mb-0">
                        "For I know the plans I have for you, declares the Lord, plans for welfare and not for evil, 
                        to give you a future and a hope." - Jeremiah 29:11
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="d-flex flex-column flex-md-row justify-content-md-end gap-3">
                        <a href="#" class="btn btn-outline-light fw-bold">
                            <i class="fas fa-plus me-2"></i> Add New
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-2">Total Activities</h6>
                        <h2 class="mb-0">12</h2>
                    </div>
                    <div class="icon-shape icon-lg bg-primary bg-opacity-10 text-primary rounded-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-success"><i class="fas fa-arrow-up"></i> 12%</span>
                    <span class="text-muted">from last month</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-2">Total Events</h6>
                        <h2 class="mb-0">15</h2>
                    </div>
                    <div class="icon-shape icon-lg bg-success bg-opacity-10 text-success rounded-3">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-success"><i class="fas fa-arrow-up"></i> 8%</span>
                    <span class="text-muted">from last month</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-2">Total Members</h6>
                        <h2 class="mb-0">{{ $stats['total_members'] ?? 0 }}</h2>
                    </div>
                    <div class="icon-shape icon-lg bg-info bg-opacity-10 text-info rounded-3">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-success"><i class="fas fa-arrow-up"></i> 5%</span>
                    <span class="text-muted">from last month</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-2">Total Users</h6>
                        <h2 class="mb-0">{{ $stats['total_users'] ?? 0 }}</h2>
                    </div>
                    <div class="icon-shape icon-lg bg-warning bg-opacity-10 text-warning rounded-3">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-success"><i class="fas fa-arrow-up"></i> 15%</span>
                    <span class="text-muted">from last month</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">Recent Activities</h5>
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 px-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="fas fa-calendar-check text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Sunday Service</h6>
                                <p class="mb-0 text-muted small">Sep 15, 2024 - 10:00 AM</p>
                            </div>
                            <span class="badge bg-primary">Upcoming</span>
                        </div>
                    </div>
                    <div class="list-group-item border-0 px-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="fas fa-book text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Bible Study</h6>
                                <p class="mb-0 text-muted small">Sep 17, 2024 - 7:00 PM</p>
                            </div>
                            <span class="badge bg-success">Scheduled</span>
                        </div>
                    </div>
                    <div class="list-group-item border-0 px-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="fas fa-praying-hands text-info"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Prayer Meeting</h6>
                                <p class="mb-0 text-muted small">Sep 19, 2024 - 6:30 PM</p>
                            </div>
                            <span class="badge bg-info">Planned</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card border-0 bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3 bg-white bg-opacity-10 p-3 rounded-3">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Total Revenue</h6>
                        <h3 class="mb-0">$12,450</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="col-12 col-xl-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent Users</h6>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['recent_users'] as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/default-avatar.png') }}" 
                                                 class="rounded-circle me-2" width="32" height="32" alt="User">
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $user->role->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-12 col-xl-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0">Recent Activities</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 border-bottom">
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <i class="fas fa-user-plus fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">New User Registration</h6>
                                    <small class="text-muted">2 min ago</small>
                                </div>
                                <p class="mb-0 small text-muted">John Doe registered as a new user.</p>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item border-0 border-bottom">
                        <div class="d-flex">
                            <div class="me-3 text-success">
                                <i class="fas fa-calendar-check fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">New Event</h6>
                                    <small class="text-muted">1 hour ago</small>
                                </div>
                                <p class="mb-0 small text-muted">New event "Sunday Service" has been added.</p>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item border-0 border-bottom">
                        <div class="d-flex">
                            <div class="me-3 text-warning">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">System Update</h6>
                                    <small class="text-muted">3 hours ago</small>
                                </div>
                                <p class="mb-0 small text-muted">System updated to version 1.2.0.</p>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item border-0">
                        <div class="d-flex">
                            <div class="me-3 text-info">
                                <i class="fas fa-comment-alt fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">New Message</h6>
                                    <small class="text-muted">5 hours ago</small>
                                </div>
                                <p class="mb-0 small text-muted">You have a new message from Sarah Johnson.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .welcome-card {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    
    .welcome-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
        pointer-events: none;
    }
    
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
    }
    
    .activity-item {
        position: relative;
        padding-left: 2rem;
        padding-bottom: 1.5rem;
        border-left: 2px solid #e3e6f0;
    }
    
    .activity-item:last-child {
        padding-bottom: 0;
    }
    
    .activity-item::before {
        content: '';
        position: absolute;
        left: -7px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #4e73df;
        border: 2px solid #fff;
    }
    
    .activity-item.activity-primary::before { background-color: #4e73df; }
    .activity-item.activity-success::before { background-color: #1cc88a; }
    .activity-item.activity-info::before { background-color: #36b9cc; }
    .activity-item.activity-warning::before { background-color: #f6c23e; }
    
    .bible-verse {
        font-style: italic;
        color: #6c757d;
        border-left: 3px solid #4e73df;
        padding: 0.5rem 1rem;
        margin: 1rem 0;
        background-color: #f8f9fc;
    }
    
    .wave {
        animation: wave 2s infinite;
        display: inline-block;
        transform-origin: 70% 70%;
    }
    
    @keyframes wave {
        0% { transform: rotate(0deg); }
        10% { transform: rotate(14deg); }
        20% { transform: rotate(-8deg); }
        30% { transform: rotate(14deg); }
        40% { transform: rotate(-4deg); }
        50% { transform: rotate(10deg); }
        60% { transform: rotate(0deg); }
        100% { transform: rotate(0deg); }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('.datatable').DataTable({
            "pageLength": 5,
            "lengthChange": false,
            "searching": false,
            "info": false,
            "order": [],
            "language": {
                "emptyTable": "No data available in table"
            },
            "responsive": true,
            "autoWidth": false
        });
        
        // Initialize charts if any
        if (typeof Chart !== 'undefined') {
            // Sample chart for retreat attendance
            const ctx = document.getElementById('attendanceChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                        datasets: [{
                            label: 'Retreat Attendance',
                            data: [12, 19, 15, 25, 22, 30, 28],
                            backgroundColor: 'rgba(78, 115, 223, 0.1)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    drawOnChartArea: true,
                                    drawTicks: false,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    stepSize: 5
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        });
    });
</script>
@endpush
