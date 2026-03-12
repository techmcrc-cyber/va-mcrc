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
                'slug' => 'view-organizations',
                'description' => 'View organizations',
                'module' => 'Organizations'
            ],
            [
                'name' => 'Create Organizations',
                'slug' => 'create-organizations',
                'description' => 'Create organizations',
                'module' => 'Organizations'
            ],
            [
                'name' => 'Edit Organizations',
                'slug' => 'edit-organizations',
                'description' => 'Edit organizations',
                'module' => 'Organizations'
            ],
            [
                'name' => 'Delete Organizations',
                'slug' => 'delete-organizations',
                'description' => 'Delete organizations',
                'module' => 'Organizations'
            ],
            
            // Leader Permissions
            [
                'name' => 'View Leaders',
                'slug' => 'view-leaders',
                'description' => 'View leaders',
                'module' => 'Leaders'
            ],
            [
                'name' => 'Create Leaders',
                'slug' => 'create-leaders',
                'description' => 'Create leaders',
                'module' => 'Leaders'
            ],
            [
                'name' => 'Edit Leaders',
                'slug' => 'edit-leaders',
                'description' => 'Edit leaders',
                'module' => 'Leaders'
            ],
            [
                'name' => 'Delete Leaders',
                'slug' => 'delete-leaders',
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
            'view-organizations',
            'create-organizations',
            'edit-organizations',
            'delete-organizations',
            'view-leaders',
            'create-leaders',
            'edit-leaders',
            'delete-leaders',
        ];

        Permission::whereIn('slug', $permissionSlugs)->delete();
    }
};
