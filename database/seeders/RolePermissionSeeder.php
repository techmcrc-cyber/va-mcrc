<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'module' => 'users'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'module' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'module' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'module' => 'users'],
            
            // Role Management
            ['name' => 'View Roles', 'slug' => 'view-roles', 'module' => 'roles'],
            ['name' => 'Create Roles', 'slug' => 'create-roles', 'module' => 'roles'],
            ['name' => 'Edit Roles', 'slug' => 'edit-roles', 'module' => 'roles'],
            ['name' => 'Delete Roles', 'slug' => 'delete-roles', 'module' => 'roles'],
            
            // Retreat Management
            ['name' => 'View Retreats', 'slug' => 'view-retreats', 'module' => 'retreats'],
            ['name' => 'Create Retreats', 'slug' => 'create-retreats', 'module' => 'retreats'],
            ['name' => 'Edit Retreats', 'slug' => 'edit-retreats', 'module' => 'retreats'],
            ['name' => 'Delete Retreats', 'slug' => 'delete-retreats', 'module' => 'retreats'],
            
            // Booking Management
            ['name' => 'View Bookings', 'slug' => 'view-bookings', 'module' => 'bookings'],
            ['name' => 'Create Bookings', 'slug' => 'create-bookings', 'module' => 'bookings'],
            ['name' => 'Edit Bookings', 'slug' => 'edit-bookings', 'module' => 'bookings'],
            ['name' => 'Delete Bookings', 'slug' => 'delete-bookings', 'module' => 'bookings'],
            
            // Reports
            ['name' => 'View Reports', 'slug' => 'view-reports', 'module' => 'reports'],
            
            // Settings
            ['name' => 'Manage Settings', 'slug' => 'manage-settings', 'module' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Create roles and assign permissions
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Has full access to all features',
                'is_super_admin' => true,
                'is_active' => true
            ]
        );

        $bookingAdmin = Role::firstOrCreate(
            ['slug' => 'booking-admin'],
            [
                'name' => 'Booking Admin',
                'description' => 'Manages bookings and related operations',
                'is_super_admin' => false,
                'is_active' => true
            ]
        );

        $retreatAdmin = Role::firstOrCreate(
            ['slug' => 'retreat-admin'],
            [
                'name' => 'Retreat Admin',
                'description' => 'Manages retreats and related operations',
                'is_super_admin' => false,
                'is_active' => true
            ]
        );

        // Assign all permissions to super admin
        $superAdmin->permissions()->sync(Permission::pluck('id')->toArray());

        // Assign permissions to booking admin
        $bookingAdmin->permissions()->sync(
            Permission::whereIn('module', ['bookings', 'reports'])
                ->orWhere('slug', 'view-users')
                ->pluck('id')
                ->toArray()
        );

        // Assign permissions to retreat admin
        $retreatAdmin->permissions()->sync(
            Permission::whereIn('module', ['retreats', 'reports'])
                ->orWhere('slug', 'view-users')
                ->pluck('id')
                ->toArray()
        );

        // Create a super admin user if not exists
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'role_id' => $superAdmin->id,
                'is_active' => true
            ]
        );
    }
}
