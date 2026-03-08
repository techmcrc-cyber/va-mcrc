<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leader;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaderController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    /**
     * Get the current organization ID based on user context.
     */
    protected function getCurrentOrganizationId()
    {
        $user = auth()->user();
        
        // Super admin can manage all organizations
        if ($user->isSuperAdmin()) {
            return request()->get('organization_id') ?? config('app.current_organization')?->id;
        }
        
        // Regular users can only manage their own organization
        return $user->organization_id;
    }

    /**
     * Display a listing of leaders.
     */
    public function index()
    {
        if (!auth()->user()->hasPermission('leaders.view')) {
            abort(403, 'Unauthorized access.');
        }
        
        $organizationId = $this->getCurrentOrganizationId();
        
        $query = Leader::with(['organization', 'creator']);
        
        // Scope by organization for non-super admins
        if ($organizationId) {
            $query->forOrganization($organizationId);
        }
        
        $leaders = $query->ordered()->paginate(20);
        
        // Get organizations for super admin filter
        $organizations = auth()->user()->isSuperAdmin() 
            ? Organization::active()->get() 
            : collect();

        return view('admin.leaders.index', compact('leaders', 'organizations'));
    }

    /**
     * Show the form for creating a new leader.
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('leaders.create')) {
            abort(403, 'Unauthorized access.');
        }
        
        $organizationId = $this->getCurrentOrganizationId();
        
        // Get organizations for super admin
        $organizations = auth()->user()->isSuperAdmin() 
            ? Organization::active()->get() 
            : Organization::where('id', $organizationId)->get();

        return view('admin.leaders.create', compact('organizations'));
    }

    /**
     * Store a newly created leader.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('leaders.create')) {
            abort(403, 'Unauthorized access.');
        }
        
        $organizationId = $this->getCurrentOrganizationId();
        
        $validated = $request->validate([
            'organization_id' => auth()->user()->isSuperAdmin() 
                ? 'required|exists:organizations,id' 
                : 'nullable',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            // Set organization_id based on user role
            $validated['organization_id'] = auth()->user()->isSuperAdmin() 
                ? $validated['organization_id'] 
                : $organizationId;
            
            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();
            $validated['is_active'] = $validated['is_active'] ?? true;
            $validated['display_order'] = $validated['display_order'] ?? 0;

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('leaders', 'public');
                $validated['image'] = $imagePath;
            }

            $leader = Leader::create($validated);

            return redirect()
                ->route('admin.leaders.index')
                ->with('success', 'Leader created successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create leader: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified leader.
     */
    public function show(Leader $leader)
    {
        if (!auth()->user()->hasPermission('leaders.view')) {
            abort(403, 'Unauthorized access.');
        }
        
        // Check if user has access to this leader's organization
        if (!auth()->user()->isSuperAdmin() && $leader->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized access to this leader.');
        }

        $leader->load(['organization', 'creator', 'updater']);
        
        return view('admin.leaders.show', compact('leader'));
    }

    /**
     * Show the form for editing the specified leader.
     */
    public function edit(Leader $leader)
    {
        if (!auth()->user()->hasPermission('leaders.edit')) {
            abort(403, 'Unauthorized access.');
        }
        
        // Check if user has access to this leader's organization
        if (!auth()->user()->isSuperAdmin() && $leader->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized access to this leader.');
        }

        $organizations = auth()->user()->isSuperAdmin() 
            ? Organization::active()->get() 
            : Organization::where('id', $leader->organization_id)->get();

        return view('admin.leaders.edit', compact('leader', 'organizations'));
    }

    /**
     * Update the specified leader.
     */
    public function update(Request $request, Leader $leader)
    {
        if (!auth()->user()->hasPermission('leaders.edit')) {
            abort(403, 'Unauthorized access.');
        }
        
        // Check if user has access to this leader's organization
        if (!auth()->user()->isSuperAdmin() && $leader->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized access to this leader.');
        }

        $validated = $request->validate([
            'organization_id' => auth()->user()->isSuperAdmin() 
                ? 'required|exists:organizations,id' 
                : 'nullable',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            // Super admin can change organization, others cannot
            if (auth()->user()->isSuperAdmin()) {
                $leader->organization_id = $validated['organization_id'];
            }
            
            $leader->name = $validated['name'];
            $leader->title = $validated['title'];
            $leader->description = $validated['description'] ?? $leader->description;
            $leader->display_order = $validated['display_order'] ?? $leader->display_order;
            $leader->is_active = $validated['is_active'] ?? $leader->is_active;
            $leader->updated_by = auth()->id();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($leader->image) {
                    Storage::disk('public')->delete($leader->image);
                }
                $imagePath = $request->file('image')->store('leaders', 'public');
                $leader->image = $imagePath;
            }

            $leader->save();

            return redirect()
                ->route('admin.leaders.index')
                ->with('success', 'Leader updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update leader: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified leader.
     */
    public function destroy(Leader $leader)
    {
        if (!auth()->user()->hasPermission('leaders.delete')) {
            abort(403, 'Unauthorized access.');
        }
        
        // Check if user has access to this leader's organization
        if (!auth()->user()->isSuperAdmin() && $leader->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized access to this leader.');
        }

        try {
            // Delete image if exists
            if ($leader->image) {
                Storage::disk('public')->delete($leader->image);
            }

            $leader->delete();

            return redirect()
                ->route('admin.leaders.index')
                ->with('success', 'Leader deleted successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete leader: ' . $e->getMessage());
        }
    }
}
