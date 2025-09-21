<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddBookingPermissions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            // Booking Permissions
            ['name' => 'View Bookings', 'slug' => 'view-bookings', 'module' => 'bookings', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Create Bookings', 'slug' => 'create-bookings', 'module' => 'bookings', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Edit Bookings', 'slug' => 'edit-bookings', 'module' => 'bookings', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Delete Bookings', 'slug' => 'delete-bookings', 'module' => 'bookings', 'created_at' => now(), 'updated_at' => now()],
        ];

        // Insert permissions if they don't exist
        foreach ($permissions as $permission) {
            if (!DB::table('permissions')->where('slug', $permission['slug'])->exists()) {
                DB::table('permissions')->insert($permission);
            }
        }

        // Get the admin role
        $adminRole = DB::table('roles')->where('slug', 'super-admin')->first();
        
        if ($adminRole) {
            // Get all permissions
            $permissionIds = DB::table('permissions')
                ->whereIn('slug', ['view-bookings', 'create-bookings', 'edit-bookings', 'delete-bookings'])
                ->pluck('id');

            // Attach permissions to admin role
            foreach ($permissionIds as $permissionId) {
                DB::table('role_permission')->insertOrIgnore([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete the permissions
        DB::table('permissions')
            ->whereIn('slug', ['view-bookings', 'create-bookings', 'edit-bookings', 'delete-bookings'])
            ->delete();
    }
}
