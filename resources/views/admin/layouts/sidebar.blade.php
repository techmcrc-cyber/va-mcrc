<!-- Sidebar -->
<div class="sidebar bg-dark text-white" id="sidebar-wrapper">
    <!-- Sidebar Header -->
    <div class="sidebar-heading text-center py-1>
        <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none d-flex flex-column align-items-center">
            <div class="position-relative mb-2">
                <img src="https://mountcarmelretreatcentre.org/wp-content/uploads/2022/02/logo_mcrc_new-1-100x118.png" alt="Mount Carmel Retreat Centre" class="img-fluid" >
            </div>
            <h5 class="mb-0 mt-2">{{ config('app.name') }}</h5>
        </a>
    </div>
    
    <!-- Sidebar User Panel -->
    <div class="user-panel px-3 py-3 border-bottom border-secondary">
        <div class="d-flex align-items-center">
            <!-- <div class="me-3">
                <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/default-avatar.png') }}" 
                     class="img-circle elevation-2" alt="User Image" style="width: 40px; height: 40px; object-fit: cover;">
            </div> -->
            <div class="info">
                <a href="{{ route('admin.profile') }}" class="d-block text-dark">
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
        
        @canany(['view-users', 'create-users', 'edit-users', 'delete-users'])
        <div class="list-group-item p-0">
            <a href="#userSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <span><i class="fas fa-users me-2"></i> User Management</span>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div class="collapse {{ request()->is('admin/users*') ? 'show' : '' }}" id="userSubmenu">
                <div class="list-group list-group-flush">
                    @can('view-users')
                    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                        <i class="fas fa-list me-2"></i> All Users
                    </a>
                    @endcan
                    @can('create-users')
                    <a href="{{ route('admin.users.create') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle me-2"></i> Add New User
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany
        
        @canany(['view-roles', 'create-roles', 'edit-roles', 'delete-roles'])
        <div class="list-group-item p-0">
            <a href="#roleSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user-shield me-2"></i> Role Management</span>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div class="collapse {{ request()->is('admin/roles*') ? 'show' : '' }}" id="roleSubmenu">
                <div class="list-group list-group-flush">
                    @can('view-roles')
                    <a href="{{ route('admin.roles.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.roles.index') ? 'active' : '' }}">
                        <i class="fas fa-list me-2"></i> All Roles
                    </a>
                    @endcan
                    @can('create-roles')
                    <a href="{{ route('admin.roles.create') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.roles.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle me-2"></i> Add New Role
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany
        
        @canany(['view-permissions', 'create-permissions', 'edit-permissions', 'delete-permissions'])
        <div class="list-group-item p-0">
            <a href="#permissionSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <span><i class="fas fa-key me-2"></i> Permissions</span>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div class="collapse {{ request()->is('admin/permissions*') ? 'show' : '' }}" id="permissionSubmenu">
                <div class="list-group list-group-flush">
                    @can('view-permissions')
                    <a href="{{ route('admin.permissions.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.permissions.index') ? 'active' : '' }}">
                        <i class="fas fa-list me-2"></i> All Permissions
                    </a>
                    @endcan
                    @can('create-permissions')
                    <a href="{{ route('admin.permissions.create') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.permissions.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle me-2"></i> Add New Permission
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany
        
        <!-- Booking Management -->
        @canany(['view-bookings', 'create-bookings', 'edit-bookings', 'delete-bookings'])
        <div class="list-group-item p-0">
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
        </div>
        @endcanany
        
        <!-- Reports -->
        @can('view-reports')
        <a href="#" class="list-group-item list-group-item-action">
            <i class="fas fa-chart-bar me-2"></i> Reports
        </a>
        @endcan
        
        <!-- Settings -->
        @can('manage-settings')
        <a href="#" class="list-group-item list-group-item-action">
            <i class="fas fa-cog me-2"></i> Settings
        </a>
        @endcan
        
        <!-- Divider -->
        <div class="sidebar-divider my-2"></div>
        
        <!-- Settings -->
        <a href="#settingsSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action">
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
        </div>
        
        <!-- Support -->
        <a href="{{ route('admin.support') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.support') ? 'active' : '' }}">
            <i class="fas fa-life-ring me-2"></i> Support
        </a>
    </div>
    
    <!-- Sidebar Footer -->
    <div class="sidebar-footer p-3 text-center border-top border-secondary">
        <div class="d-flex justify-content-center gap-2 mb-2">
            <a href="#" class="text-white-50" data-bs-toggle="tooltip" title="Help Center">
                <i class="fas fa-question-circle"></i>
            </a>
            <a href="#" class="text-white-50" data-bs-toggle="tooltip" title="Documentation">
                <i class="fas fa-book"></i>
            </a>
            <a href="#" class="text-white-50" data-bs-toggle="tooltip" title="Feedback">
                <i class="fas fa-comment-alt"></i>
            </a>
        </div>
        <small class="text-muted d-block">v1.0.0</small>
        <small class="text-muted">
            <i class="fas fa-heart text-danger"></i> Made with prayer
        </small>
    </div>
</div>
