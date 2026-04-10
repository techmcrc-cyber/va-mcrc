<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first super admin user as creator
        $superAdmin = User::whereHas('role', function ($query) {
            $query->where('is_super_admin', true);
        })->first();

        // Create Mount Carmel Retreat Centre organization
        $organization = Organization::create([
            'name' => 'Mount Carmel Retreat Centre',
            'slug' => 'mount-carmel-retreat-centre',
            'is_verified' => true,
            'is_active' => true,
            'created_by' => $superAdmin?->id,
            'updated_by' => $superAdmin?->id,
        ]);

        $role = Role::firstOrCreate([
            'name' => 'Organization Admin',
            'slug' => 'organization-admin',
            'description' => 'Administrator for organization',
            'is_super_admin' => false
        ]);

        // Create MCRC Admin user for the organization
        User::create([
            'role_id' => $role->id,
            'organization_id' => $organization->id,
            'name' => 'MCRC Admin',
            'email' => 'mcrcadmin@gmail.com',
            'password' => Hash::make('password@123'),
            'is_active' => true,
        ]);
    }
}
