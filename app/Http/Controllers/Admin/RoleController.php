<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('view-roles');
        
        if ($request->ajax()) {
            $query = Role::with('permissions');
            
            // Handle search
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // Handle sorting
            if ($request->has('order')) {
                $column = $request->input('order.0.column');
                $dir = $request->input('order.0.dir');
                $columns = ['id', 'name', 'slug', 'description', 'is_active'];
                
                if (isset($columns[$column])) {
                    $query->orderBy($columns[$column], $dir);
                } else {
                    $query->orderBy('id', 'asc');
                }
            } else {
                $query->orderBy('id', 'asc');
            }
            
            $totalData = $query->count();
            $limit = $request->input('length', 25);
            $start = $request->input('start', 0);
            
            $roles = $query->offset($start)
                         ->limit($limit)
                         ->get();
            
            $data = [];
            foreach ($roles as $role) {
                $nestedData = [];
                $nestedData['id'] = $role->id;
                $nestedData['name'] = $role->name;
                $nestedData['slug'] = $role->slug;
                $nestedData['description'] = $role->description ?: 'N/A';
                
                // Status with badge
                $statusBadge = '';
                if ($role->is_active) {
                    $statusBadge = '<span class="badge bg-success text-white"><i class="fas fa-check-circle me-1"></i> Active</span>';
                } else {
                    $statusBadge = '<span class="badge bg-secondary text-white"><i class="fas fa-times-circle me-1"></i> Inactive</span>';
                }
                
                // Add Super Admin badge if applicable
                if ($role->is_super_admin) {
                    $statusBadge .= ' <span class="badge bg-gradient-primary text-white ms-1" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
                    $statusBadge .= '<i class="fas fa-crown me-1"></i> Super Admin</span>';
                }
                
                $nestedData['status'] = $statusBadge;
                
                // Actions
                $actions = '<div class="btn-group" role="group">';
                
                // Edit button - check permission
                if (auth()->user()->can('edit-roles')) {
                    $actions .= '<a href="' . route('admin.roles.edit', $role) . '" class="btn btn-sm btn-primary">';
                    $actions .= '<i class="fas fa-edit"></i></a>';
                }
                
                // Delete button - check permission and if not super admin
                if (auth()->user()->can('delete-roles') && !$role->is_super_admin) {
                    $actions .= '<form action="' . route('admin.roles.destroy', $role) . '" method="POST" class="d-inline">';
                    $actions .= csrf_field();
                    $actions .= method_field('DELETE');
                    $actions .= '<button type="submit" class="btn btn-sm btn-danger" ';
                    $actions .= 'onclick="return confirm(\'Are you sure you want to delete this role?\')">';
                    $actions .= '<i class="fas fa-trash"></i></button></form>';
                }
                
                $actions .= '</div>';
                $nestedData['actions'] = $actions;
                
                $data[] = $nestedData;
            }
            
            $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalData),
                "data"            => $data
            ];
            
            return response()->json($json_data);
        }
        
        return view('admin.roles.index');
    }

    /**
     * Show the form for creating a new role.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create-roles');
        
        $permissions = Permission::where('is_active', true)
            ->orderBy('module')
            ->get()
            ->groupBy('module');
            
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create-roles');
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $role = Role::create([
                'name' => $validated['name'],
                'slug' => \Illuminate\Support\Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'),
            ]);

            $role->permissions()->sync($validated['permissions']);
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\View\View
     */
    public function edit(Role $role)
    {
        $this->authorize('edit-roles');
        
        if ($role->is_super_admin) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Super admin role cannot be edited.');
        }
        
        $permissions = Permission::where('is_active', true)
            ->orderBy('module')
            ->get()
            ->groupBy('module');
            
        $role->load('permissions');
        
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('edit-roles');
        
        if ($role->is_super_admin) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Super admin role cannot be edited.');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        DB::transaction(function () use ($role, $validated, $request) {
            $role->update([
                'name' => $validated['name'],
                'slug' => \Illuminate\Support\Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'is_active' => request()->has('is_active'),
            ]);

            $role->permissions()->sync($validated['permissions']);
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete-roles');
        
        if ($role->is_super_admin) {
            return back()->with('error', 'Super admin role cannot be deleted.');
        }
        
        if ($role->users()->exists()) {
            return back()->with('error', 'Cannot delete role with associated users.');
        }
        
        $role->delete();
        
        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
