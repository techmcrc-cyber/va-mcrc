<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            // Organization Permissions
            [
                'name' => 'View Organizations',
                'slug' => 'organizations.view',
                'description' => 'View organizations',
                'module' => 'Organizations'
            ],
            [
                'name' => 'Create Organizations',
                'slug' => 'organizations.create',
                'description' => 'Create organizations',
                'module' => 'Organizations'
            ],
            [
                'name' => 'Edit Organizations',
                'slug' => 'organizations.edit',
                'description' => 'Edit organizations',
                'module' => 'Organizations'
            ],
            [
                'name' => 'Delete Organizations',
                'slug' => 'organizations.delete',
                'description' => 'Delete organizations',
                'module' => 'Organizations'
            ],
            
            // Leader Permissions
            [
                'name' => 'View Leaders',
                'slug' => 'leaders.view',
                'description' => 'View leaders',
                'module' => 'Leaders'
            ],
            [
                'name' => 'Create Leaders',
                'slug' => 'leaders.create',
                'description' => 'Create leaders',
                'module' => 'Leaders'
            ],
            [
                'name' => 'Edit Leaders',
                'slug' => 'leaders.edit',
                'description' => 'Edit leaders',
                'module' => 'Leaders'
            ],
            [
                'name' => 'Delete Leaders',
                'slug' => 'leaders.delete',
                'description' => 'Delete leaders',
                'module' => 'Leaders'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'module' => $permission['module'],
                    'is_active' => true
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionSlugs = [
            'organizations.view',
            'organizations.create',
            'organizations.edit',
            'organizations.delete',
            'leaders.view',
            'leaders.create',
            'leaders.edit',
            'leaders.delete',
        ];

        Permission::whereIn('slug', $permissionSlugs)->delete();
    }
};
