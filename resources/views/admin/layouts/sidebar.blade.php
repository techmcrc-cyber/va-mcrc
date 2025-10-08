<!-- Sidebar -->
<div class="sidebar border-end" id="sidebar-wrapper" style="background-color: #edf2f7;">
    <!-- Sidebar Header -->
    <div class="sidebar-heading text-center py-1>
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none d-flex flex-column align-items-center" style="color: #5a5c69;">
            <div class="position-relative mb-2">
                <img src="https://mountcarmelretreatcentre.org/wp-content/uploads/2022/02/logo_mcrc_new-1-100x118.png" alt="Mount Carmel Retreat Centre" class="img-fluid" >
            </div>
            <h5 class="mb-0 mt-2">{{ config('app.name') }}</h5>
        </a>
    </div>
    
    <!-- Sidebar User Panel -->
    <div class="user-panel px-3 py-1 border-bottom text-center">
        <div class="d-flex align-items-center justify-content-center">
            <div class="info">
                <a href="{{ route('admin.profile') }}" class="d-block fw-bold" style="color: #5a5c69;">
                    {{ Auth::user()->name }}
                </a>
            </div>
        </div>
    </div>
    <!-- Sidebar Menu -->
    <nav class="mt-3">
        <div class="list-group list-group-flush">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            @can('view-retreats')
            <a href="{{ route('admin.retreats.index') }}" 
               class="list-group-item list-group-item-action {{ request()->is('admin/retreats*') ? 'active' : '' }}">
                <i class="fas fa-user-shield me-2"></i> Retreats
            </a>
            @endcan
            @can('view-bookings')
            <!-- Bookings Tree Menu -->
            <a href="#bookingSubmenu" data-bs-toggle="collapse" 
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->is('admin/bookings*') ? 'active' : '' }}" 
               aria-expanded="{{ request()->is('admin/bookings*') ? 'true' : 'false' }}">
                <span><i class="fas fa-calendar-check me-2"></i> Bookings</span>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div class="collapse {{ request()->is('admin/bookings*') ? 'show' : '' }}" id="bookingSubmenu">
                <div class="list-group list-group-flush">
                    @can('view-bookings')
                    <a href="{{ route('admin.bookings.active') }}" 
                       class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.bookings.active') || (request()->routeIs('admin.bookings.show') && !request()->get('archived')) || (request()->routeIs('admin.bookings.edit') && !request()->get('archived')) || request()->routeIs('admin.bookings.create') ? 'active' : '' }}">
                        <i class="fas fa-list me-2"></i> Active List
                    </a>
                    @endcan
                    @can('view-bookings')
                    <a href="{{ route('admin.bookings.archive') }}" 
                       class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.bookings.archive') ? 'active' : '' }}">
                        <i class="fas fa-archive me-2"></i> Archive List
                    </a>
                    @endcan
                    @can('create-bookings')
                    <a href="{{ route('admin.bookings.import') }}" 
                       class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.bookings.import*') ? 'active' : '' }}">
                        <i class="fas fa-file-import me-2"></i> Import
                    </a>
                    @endcan
                    @can('view-bookings')
                    <a href="{{ route('admin.bookings.export') }}" 
                       class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.bookings.export*') ? 'active' : '' }}">
                        <i class="fas fa-file-export me-2"></i> Export
                    </a>
                    @endcan
                </div>
            </div>
            @endcan
            @if(Auth::user()->role && Auth::user()->role->is_super_admin)
            <a href="{{ route('admin.special-bookings.index') }}" 
               class="list-group-item list-group-item-action {{ request()->is('admin/special-bookings*') ? 'active' : '' }}">
                <i class="fas fa-star me-2"></i> Special Bookings
            </a>
            @endif
            @can('view-criteria')
            <a href="{{ route('admin.criteria.index') }}" 
               class="list-group-item list-group-item-action {{ request()->is('admin/criteria*') ? 'active' : '' }}">
                <i class="fas fa-filter me-2"></i> Criteria
            </a>
            @endcan
        
            @can('view-users')
            <a href="{{ route('admin.users.index') }}" 
               class="list-group-item list-group-item-action {{ request()->is('admin/users*') ? 'active' : '' }}">
                <i class="fas fa-users me-2"></i> Users
            </a>
            @endcan
        
            @can('view-permissions')
            <a href="{{ route('admin.permissions.index') }}" 
               class="list-group-item list-group-item-action {{ request()->is('admin/permissions*') ? 'active' : '' }}">
                <i class="fas fa-key me-2"></i> Permissions
            </a>
            @endcan

            @can('view-roles')
            <a href="{{ route('admin.roles.index') }}" 
               class="list-group-item list-group-item-action {{ request()->is('admin/roles*') ? 'active' : '' }}">
                <i class="fas fa-user-shield me-2"></i> Roles
            </a>
            @endcan

            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
            @csrf
                <button type="submit" class="dropdown-item list-group-item list-group-item-action">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        

            <!-- Booking Management -->
            @canany(['view-bookings', 'create-bookings', 'edit-bookings', 'delete-bookings'])
           <!--  <div class="list-group-item p-0">
                <a href="#bookingSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-calendar-check me-2"></i> Bookings</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->is('admin/bookings*') ? 'show' : '' }}" id="bookingSubmenu">
                    <div class="list-group list-group-flush">
                        @can('view-bookings')
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-list me-2"></i> All Bookings
                        </a>
                        @endcan
                        @can('create-bookings')
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus-circle me-2"></i> Create Booking
                        </a>
                        @endcan
                    </div>
                </div>
            </div> -->
            @endcanany
        
        <!-- Divider -->
        <div class="sidebar-divider my-2"></div>
        
        <!-- Settings -->
<!--         <a href="#settingsSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action">
            <i class="fas fa-cog me-2"></i> Settings
            <i class="fas fa-chevron-right float-end mt-1"></i>
        </a>
        <div class="collapse {{ request()->is('admin/settings/*') ? 'show' : '' }}" id="settingsSubmenu">
            <div class="list-group list-group-flush bg-dark">
                <a href="{{ route('admin.settings.general') }}" class="list-group-item list-group-item-action bg-dark text-white-50 py-2 {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                    <i class="fas fa-sliders-h me-2"></i> General
                </a>
                <a href="{{ route('admin.settings.email') }}" class="list-group-item list-group-item-action bg-dark text-white-50 py-2 {{ request()->routeIs('admin.settings.email') ? 'active' : '' }}">
                    <i class="fas fa-envelope me-2"></i> Email
                </a>
                <a href="{{ route('admin.settings.payment') }}" class="list-group-item list-group-item-action bg-dark text-white-50 py-2 {{ request()->routeIs('admin.settings.payment') ? 'active' : '' }}">
                    <i class="fas fa-credit-card me-2"></i> Payment
                </a>
                <a href="{{ route('admin.settings.notification') }}" class="list-group-item list-group-item-action bg-dark text-white-50 py-2 {{ request()->routeIs('admin.settings.notification') ? 'active' : '' }}">
                    <i class="fas fa-bell me-2"></i> Notifications
                </a>
            </div>
        </div> -->
        
        <!-- Support -->
      <!--   <a href="{{ route('admin.support') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.support') ? 'active' : '' }}">
            <i class="fas fa-life-ring me-2"></i> Support
        </a> -->
    </div>
    
    <!-- Sidebar Footer -->
    <div class="sidebar-footer p-3 text-center border-top">
       <!--  <div class="d-flex justify-content-center gap-2 mb-2">
            <a href="#" class="text-white-50" data-bs-toggle="tooltip" title="Help Center">
                <i class="fas fa-question-circle"></i>
            </a>
            <a href="#" class="text-white-50" data-bs-toggle="tooltip" title="Documentation">
                <i class="fas fa-book"></i>
            </a>
            <a href="#" class="text-white-50" data-bs-toggle="tooltip" title="Feedback">
                <i class="fas fa-comment-alt"></i>
            </a>
        </div> -->
        <small class="text-muted d-block">v1.0.0</small>
        <small class="text-muted">
            <i class="fas fa-heart text-danger"></i> Made with prayer
        </small>
    </div>
</div>
