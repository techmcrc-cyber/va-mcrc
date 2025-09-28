<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('view-users');
        
        if ($request->ajax()) {
            $query = User::with('role');
            
            // Handle search
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
            
            // Handle sorting
            if ($request->has('order')) {
                $column = $request->input('order.0.column');
                $dir = $request->input('order.0.dir');
                $columns = ['id', 'name', 'email', 'role_id', 'is_active'];
                
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
            
            $users = $query->offset($start)
                         ->limit($limit)
                         ->get();
            
            $data = [];
            foreach ($users as $user) {
                $nestedData = [];
                $nestedData['id'] = $user->id;
                $nestedData['name'] = $user->name;
                $nestedData['email'] = $user->email;
                
                // Role with badge
                $roleBadge = '';
                if ($user->role) {
                    $roleName = $user->role->name;
                    list($bgColor, $textColor, $borderColor) = \App\Helpers\RoleHelper::getRoleColors($roleName);
                    $isSuperAdmin = $user->isSuperAdmin();
                    
                    $roleBadge = '<span class="badge shadow-sm" ';
                    $roleBadge .= 'style="background-color: ' . $bgColor . '; ';
                    $roleBadge .= 'color: ' . $textColor . '; ';
                    $roleBadge .= 'border: 1px solid ' . $borderColor . ';';
                    
                    if ($isSuperAdmin) {
                        $roleBadge .= 'background: linear-gradient(135deg, ' . $bgColor . ' 0%, ' . $borderColor . ' 100%);';
                    }
                    
                    $roleBadge .= '">';
                    
                    if ($isSuperAdmin) {
                        $roleBadge .= '<i class="fas fa-crown me-1"></i>';
                    } else {
                        $roleBadge .= '<i class="fas fa-user-shield me-1"></i>';
                    }
                    
                    $roleBadge .= e($roleName) . '</span>';
                } else {
                    $roleBadge = '<span class="badge bg-secondary">';
                    $roleBadge .= '<i class="fas fa-user-slash me-1"></i> No Role';
                    $roleBadge .= '</span>';
                }
                
                $nestedData['role'] = $roleBadge;
                
                // Status with badge
                if ($user->is_active) {
                    $nestedData['status'] = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>';
                } else {
                    $nestedData['status'] = '<span class="badge bg-secondary"><i class="fas fa-times-circle me-1"></i> Inactive</span>';
                }
                
                // Actions
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('admin.users.edit', $user) . '" class="btn btn-sm btn-primary">';
                $actions .= '<i class="fas fa-edit"></i></a>';
                
                if (!$user->isSuperAdmin() && auth()->id() !== $user->id) {
                    $actions .= '<form action="' . route('admin.users.destroy', $user) . '" method="POST" class="d-inline">';
                    $actions .= csrf_field();
                    $actions .= method_field('DELETE');
                    $actions .= '<button type="submit" class="btn btn-sm btn-danger" ';
                    $actions .= 'onclick="return confirm(\'Are you sure you want to delete this user? This action cannot be undone.\')">';
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
        
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create-users');
        
        $roles = Role::where('is_active', true)->get();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create-users');
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $this->authorize('edit-users');
        
        $roles = Role::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('edit-users');
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $this->authorize('delete-users');
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
