@extends('admin.layouts.app')

@section('page-title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
<div class="p-4">
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
                        <h6 class="text-uppercase text-muted mb-2">Total Users</h6>
                        <h2 class="mb-0 counter-number">{{ $stats['total_users'] }}</h2>
                    </div>
                    <div class="icon-shape icon-lg bg-primary bg-opacity-10 text-primary rounded-3">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-3">
                    @if($stats['user_growth'] >= 0)
                        <span class="text-success"><i class="fas fa-arrow-up"></i> {{ $stats['user_growth'] }}%</span>
                    @else
                        <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ abs($stats['user_growth']) }}%</span>
                    @endif
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
                        <h6 class="text-uppercase text-muted mb-2">Total Retreats</h6>
                        <h2 class="mb-0 counter-number">{{ $stats['total_retreats'] }}</h2>
                    </div>
                    <div class="icon-shape icon-lg bg-success bg-opacity-10 text-success rounded-3">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="mt-3">
                    @if($stats['retreat_growth'] >= 0)
                        <span class="text-success"><i class="fas fa-arrow-up"></i> {{ $stats['retreat_growth'] }}%</span>
                    @else
                        <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ abs($stats['retreat_growth']) }}%</span>
                    @endif
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
                        <h6 class="text-uppercase text-muted mb-2">Total Bookings</h6>
                        <h2 class="mb-0 counter-number">{{ $stats['total_bookings'] }}</h2>
                    </div>
                    <div class="icon-shape icon-lg bg-info bg-opacity-10 text-info rounded-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="mt-3">
                    @if($stats['booking_growth'] >= 0)
                        <span class="text-success"><i class="fas fa-arrow-up"></i> {{ $stats['booking_growth'] }}%</span>
                    @else
                        <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ abs($stats['booking_growth']) }}%</span>
                    @endif
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
                        <h6 class="text-uppercase text-muted mb-2">Admin Users</h6>
                        <h2 class="mb-0 counter-number">{{ $stats['total_admin_users'] }}</h2>
                    </div>
                    <div class="icon-shape icon-lg bg-warning bg-opacity-10 text-warning rounded-3">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
                <div class="mt-3">
                    @if($stats['admin_user_growth'] >= 0)
                        <span class="text-success"><i class="fas fa-arrow-up"></i> {{ $stats['admin_user_growth'] }}%</span>
                    @else
                        <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ abs($stats['admin_user_growth']) }}%</span>
                    @endif
                    <span class="text-muted">from last month</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Statistics Summary -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body">
                <div class="icon-shape icon-lg bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3">
                    <i class="fas fa-mountain"></i>
                </div>
                <h3 class="mb-1">{{ $retreatStats['upcoming'] }}</h3>
                <p class="text-muted mb-0">Upcoming Retreats</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body">
                <div class="icon-shape icon-lg bg-info bg-opacity-10 text-info rounded-circle mx-auto mb-3">
                    <i class="fas fa-play-circle"></i>
                </div>
                <h3 class="mb-1">{{ $retreatStats['ongoing'] }}</h3>
                <p class="text-muted mb-0">Ongoing Retreats</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body">
                <div class="icon-shape icon-lg bg-warning bg-opacity-10 text-warning rounded-circle mx-auto mb-3">
                    <i class="fas fa-star"></i>
                </div>
                <h3 class="mb-1">{{ $retreatStats['featured'] }}</h3>
                <p class="text-muted mb-0">Featured Retreats</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body">
                <div class="icon-shape icon-lg bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="mb-1">{{ $bookingStats['primary_participants'] }}</h3>
                <p class="text-muted mb-0">Primary Participants</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Activities -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">Recent Activities</h5>
                <div class="list-group list-group-flush">
                    @forelse($activities as $activity)
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-{{ $activity['color'] }} bg-opacity-10 p-3 rounded-3 me-3">
                                    <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $activity['description'] }}</h6>
                                    <p class="mb-0 text-muted small">
                                        by {{ $activity['user'] }} • {{ $activity['created_at']->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="badge bg-{{ $activity['color'] }}">{{ ucfirst($activity['type']) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item border-0 px-0">
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No recent activities found.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

<!-- Booking Trends Chart -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Monthly Booking Trends</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-chart-line me-1"></i> Last 6 Months
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Last 3 Months</a></li>
                            <li><a class="dropdown-item" href="#">Last 6 Months</a></li>
                            <li><a class="dropdown-item" href="#">Last 12 Months</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="bookingsChart"></canvas>
                </div>
                
                <!-- Chart Summary Stats -->
                <div class="row mt-4 pt-3 border-top">
                    <div class="col-md-3 text-center">
                        <div class="d-flex flex-column">
                            <span class="h4 mb-0 text-primary counter-number">{{ $bookingStats['new_this_month'] }}</span>
                            <span class="text-muted small">This Month</span>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex flex-column">
                            <span class="h4 mb-0 text-success counter-number">{{ $bookingStats['primary_participants'] }}</span>
                            <span class="text-muted small">Primary Bookings</span>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex flex-column">
                            <span class="h4 mb-0 text-info counter-number">{{ $bookingStats['additional_participants'] }}</span>
                            <span class="text-muted small">Additional</span>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex flex-column">
                            @if($bookingStats['growth_percentage'] >= 0)
                                <span class="h4 mb-0 text-success">+{{ $bookingStats['growth_percentage'] }}%</span>
                            @else
                                <span class="h4 mb-0 text-danger">{{ $bookingStats['growth_percentage'] }}%</span>
                            @endif
                            <span class="text-muted small">Growth</span>
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
    /* Welcome Card Styles */
    .welcome-card {
        background: linear-gradient(136deg, #ba4165 0%, #700000 100%);        
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
        border: none;
        transition: all 0.3s ease;
    }
    
    .welcome-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
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
        transition: all 0.6s ease;
    }
    
    .welcome-card:hover::before {
        transform: rotate(45deg);
    }
    
    /* Stats Cards */
    .card {
        border: none;
        transition: all 0.3s ease;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .card .card-body {
        padding: 1.5rem;
    }
    
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .card:hover .icon-shape {
        transform: scale(1.1);
    }
    
    /* Activity Timeline */
    .activity-item {
        position: relative;
        padding-left: 1.5rem;
        padding-bottom: 1.5rem;
        border-left: 2px solid #e3e6f0;
        transition: all 0.3s ease;
    }
    
    .activity-item:hover {
        background-color: #f8f9fc;
        border-radius: 0.5rem;
    }
    
    .activity-item:last-child {
        padding-bottom: 0;
    }
    
    .activity-item::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 0.25rem;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: #4e73df;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #4e73df;
        transition: all 0.3s ease;
    }
    
    .activity-item:hover::before {
        transform: scale(1.2);
    }
    
    .activity-item.activity-primary::before { background-color: #4e73df; box-shadow: 0 0 0 2px #4e73df; }
    .activity-item.activity-success::before { background-color: #1cc88a; box-shadow: 0 0 0 2px #1cc88a; }
    .activity-item.activity-info::before { background-color: #36b9cc; box-shadow: 0 0 0 2px #36b9cc; }
    .activity-item.activity-warning::before { background-color: #f6c23e; box-shadow: 0 0 0 2px #f6c23e; }
    
    /* Bible Verse */
    .bible-verse {
        font-style: italic;
        color: #4a6fa5;
        border-left: 4px solid #4e73df;
        padding: 1rem 1.5rem;
        margin: 1.5rem 0;
        background-color: #f8fafd;
        border-radius: 0 0.5rem 0.5rem 0;
        position: relative;
        overflow: hidden;
    }
    
    .bible-verse::before {
        content: '""';
        position: absolute;
        font-size: 5rem;
        color: rgba(78, 115, 223, 0.05);
        top: -1.5rem;
        left: 0.5rem;
        line-height: 1;
        font-family: Georgia, serif;
    }
    
    /* Wave Animation */
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
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .welcome-card {
            text-align: center;
        }
        
        .welcome-card .btn {
            width: 100%;
            margin-top: 1rem;
        }
        
        .card .d-flex {
            flex-direction: column;
            text-align: center;
        }
        
        .icon-shape {
            margin: 0 auto 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
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
            // Monthly Bookings Chart
            const ctx = document.getElementById('bookingsChart');
            if (ctx) {
                const monthlyData = @json($monthlyBookings);
                const labels = monthlyData.map(item => item.month);
                const data = monthlyData.map(item => item.count);
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Monthly Bookings',
                            data: data,
                            backgroundColor: 'rgba(78, 115, 223, 0.1)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: 'rgba(78, 115, 223, 1)',
                                borderWidth: 1,
                                cornerRadius: 6,
                                displayColors: false,
                                callbacks: {
                                    title: function(context) {
                                        return context[0].label;
                                    },
                                    label: function(context) {
                                        return `${context.parsed.y} booking${context.parsed.y !== 1 ? 's' : ''}`;
                                    }
                                }
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
                                    stepSize: Math.max(1, Math.ceil(Math.max(...data) / 5)),
                                    color: '#858796',
                                    font: {
                                        size: 12
                                    },
                                    callback: function(value) {
                                        return value;
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#858796',
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            }
        }
        
        // Animate counter numbers
        $('.counter-number').each(function() {
            const $this = $(this);
            const countTo = parseInt($this.text());
            const duration = 2000;
            
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: duration,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(countTo);
                }
            });
        });
    });
</script>
@endpush
