<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrganizationController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    /**
     * Display a listing of organizations.
     */
    public function index()
    {
        if (!auth()->user()->hasPermission('view-organizations')) {
            abort(403, 'Unauthorized access.');
        }
        
        $organizations = Organization::with(['creator', 'users'])
            ->withCount(['users', 'retreats', 'bookings'])
            ->latest()
            ->paginate(20);

        return view('admin.organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('create-organizations')) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('admin.organizations.create');
    }

    /**
     * Store a newly created organization.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('create-organizations')) {
            abort(403, 'Unauthorized access.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug|alpha_dash',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            
            // Organization Owner Details
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:8|confirmed',
            'owner_phone' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            // Create organization
            $organization = Organization::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['slug']),
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_verified' => $validated['is_verified'] ?? true,
                'is_active' => $validated['is_active'] ?? true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('organizations/logos', 'public');
                $organization->update(['logo' => $logoPath]);
            }

            // Get or create Organization Admin role
            $orgAdminRole = Role::firstOrCreate(
                ['name' => 'Organization Admin'],
                [
                    'slug' => 'organization-admin',
                    'description' => 'Administrator for organization',
                    'is_super_admin' => false
                ]
            );

            // Create organization owner user
            $owner = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'password' => Hash::make($validated['owner_password']),
                'phone' => $validated['owner_phone'] ?? null,
                'role_id' => $orgAdminRole->id,
                'organization_id' => $organization->id,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.organizations.index')
                ->with('success', 'Organization created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create organization: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified organization.
     */
    public function show(Organization $organization)
    {
        if (!auth()->user()->hasPermission('view-organizations')) {
            abort(403, 'Unauthorized access.');
        }
        
        $organization->load(['users', 'retreats', 'bookings', 'leaders']);
        
        return view('admin.organizations.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified organization.
     */
    public function edit(Organization $organization)
    {
        if (!auth()->user()->hasPermission('edit-organizations')) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('admin.organizations.edit', compact('organization'));
    }

    /**
     * Update the specified organization.
     */
    public function update(Request $request, Organization $organization)
    {
        if (!auth()->user()->hasPermission('edit-organizations')) {
            abort(403, 'Unauthorized access.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|alpha_dash|unique:organizations,slug,' . $organization->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            $organization->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['slug']),
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_verified' => $validated['is_verified'] ?? $organization->is_verified,
                'is_active' => $validated['is_active'] ?? $organization->is_active,
                'updated_by' => auth()->id(),
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($organization->logo) {
                    \Storage::disk('public')->delete($organization->logo);
                }
                $logoPath = $request->file('logo')->store('organizations/logos', 'public');
                $organization->update(['logo' => $logoPath]);
            }

            return redirect()
                ->route('admin.organizations.index')
                ->with('success', 'Organization updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update organization: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified organization.
     */
    public function destroy(Organization $organization)
    {
        if (!auth()->user()->hasPermission('delete-organizations')) {
            abort(403, 'Unauthorized access.');
        }
        
        try {
            $organization->delete();

            return redirect()
                ->route('admin.organizations.index')
                ->with('success', 'Organization deleted successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete organization: ' . $e->getMessage());
        }
    }
}