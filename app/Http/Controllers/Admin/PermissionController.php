<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('view-permissions');
        
        if ($request->ajax()) {
            $query = Permission::query();
            
            // Handle search
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('module', 'like', "%{$search}%");
                });
            }
            
            // Handle sorting
            if ($request->has('order')) {
                $column = $request->input('order.0.column');
                $dir = $request->input('order.0.dir');
                $columns = ['id', 'name', 'slug', 'module', 'is_active'];
                
                if (isset($columns[$column])) {
                    $query->orderBy($columns[$column], $dir);
                } else {
                    $query->latest();
                }
            } else {
                $query->latest();
            }
            
            $totalData = $query->count();
            $limit = $request->input('length', 25);
            $start = $request->input('start', 0);
            
            $permissions = $query->offset($start)
                               ->limit($limit)
                               ->get();
            
            $data = [];
            foreach ($permissions as $permission) {
                $nestedData = [];
                $nestedData['id'] = $permission->id;
                $nestedData['name'] = $permission->name;
                $nestedData['slug'] = $permission->slug;
                $nestedData['module'] = $permission->module ?: 'N/A';
                $nestedData['status'] = $permission->is_active 
                    ? '<span class="badge bg-success text-white"><i class="fas fa-check-circle me-1"></i> Active</span>'
                    : '<span class="badge bg-secondary text-white"><i class="fas fa-times-circle me-1"></i> Inactive</span>';
                
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('admin.permissions.edit', $permission) . '" class="btn btn-sm btn-primary">';
                $actions .= '<i class="fas fa-edit"></i></a>';
                
                if($permission->is_deletable) {
                    $actions .= '<form action="' . route('admin.permissions.destroy', $permission) . '" method="POST" class="d-inline">';
                    $actions .= csrf_field();
                    $actions .= method_field('DELETE');
                    $actions .= '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this permission? This action cannot be undone.\')">';
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
        
        return view('admin.permissions.index');
    }

    /**
     * Show the form for creating a new permission.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create-permissions');
        
        $modules = Permission::distinct('module')
            ->pluck('module')
            ->toArray();
            
        return view('admin.permissions.create', compact('modules'));
    }

    /**
     * Store a newly created permission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create-permissions');
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:permissions'],
            'module' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'module' => $validated['module'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified permission.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\View\View
     */
    public function edit(Permission $permission)
    {
        $this->authorize('edit-permissions');
        
        $modules = Permission::distinct('module')
            ->pluck('module')
            ->toArray();
            
        return view('admin.permissions.edit', compact('permission', 'modules'));
    }

    /**
     * Update the specified permission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Permission $permission)
    {
        $this->authorize('edit-permissions');
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:permissions,slug,' . $permission->id],
            'module' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $permission->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'module' => $validated['module'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Permission $permission)
    {
        $this->authorize('delete-permissions');
        
        if ($permission->roles()->exists()) {
            return back()->with('error', 'Cannot delete permission that is assigned to roles.');
        }
        
        $permission->delete();
        
        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
