<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RetreatRequest;
use App\Models\Retreat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RetreatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $organizationId = $user->organization_id;
        
        if ($request->ajax()) {
            $query = Retreat::query();
            
            // Apply organization filtering only for users with organization_id (not super admin or users without org)
            if (!$isSuperAdmin && $organizationId) {
                $query->where('organization_id', $organizationId);
            }
            
            // Handle organization filter (for Super Admins and users without organization_id)
            if (($isSuperAdmin || !$organizationId) && $request->has('organization_filter') && !empty($request->organization_filter)) {
                $query->where('organization_id', $request->organization_filter);
            }
            
            // Handle status filter
            if ($request->has('status_filter') && !empty($request->status_filter)) {
                $filter = $request->status_filter;
                
                switch ($filter) {
                    case 'active':
                        $query->where('is_active', true);
                        break;
                    case 'inactive':
                        $query->where('is_active', false);
                        break;
                    case 'featured':
                        $query->where('is_featured', true);
                        break;
                    case 'deleted':
                        $query->onlyTrashed();
                        break;
                    case 'all':
                    default:
                        // Show all non-deleted retreats
                        break;
                }
            }
            
            // Handle search
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('timings', 'like', "%{$search}%")
                      ->orWhere('special_remarks', 'like', "%{$search}%");
                });
            }
            
            // Handle sorting
            if ($request->has('order')) {
                $column = $request->input('order.0.column');
                $dir = $request->input('order.0.dir');
                $columns = ['title', 'start_date', 'timings', 'seats', 'criteria', 'is_active'];
                
                if (isset($columns[$column])) {
                    $query->orderBy($columns[$column], $dir);
                } else {
                    $query->orderBy('end_date', 'desc');
                }
            } else {
                $query->orderBy('end_date', 'desc');
            }
            
            $totalData = $query->count();
            $limit = $request->input('length', 25);
            $start = $request->input('start', 0);
            
            $retreats = $query->with(['criteriaRelation', 'organization'])
                            ->offset($start)
                            ->limit($limit)
                            ->get();
            
            $data = [];
            foreach ($retreats as $retreat) {
                $nestedData = [];
                
                // Title with organization tag (for Super Admins and users without organization_id)
                $titleWithOrg = $retreat->title;
                $user = auth()->user();
                if (($user->isSuperAdmin() || !$user->organization_id) && $retreat->organization) {
                    $titleWithOrg .= ' <span class="badge bg-info ms-2">' . e($retreat->organization->name) . '</span>';
                }
                $nestedData['title'] = $titleWithOrg;
                
                $nestedData['date'] = $retreat->start_date->format('M d, Y') . ' - ' . $retreat->end_date->format('M d, Y');
                $nestedData['end_date'] = $retreat->end_date->format('Y-m-d'); // For sorting
                $nestedData['timings'] = $retreat->timings;
                $nestedData['seats'] = $retreat->seats;
                $nestedData['criteria'] = $retreat->criteriaRelation ? $retreat->criteriaRelation->name : '-';
                
                // WhatsApp Channel Link
                if ($retreat->whatsapp_channel_link) {
                    $nestedData['whatsapp_link'] = '<a href="' . e($retreat->whatsapp_channel_link) . '" target="_blank" class="btn btn-success btn-sm" title="WhatsApp Channel">';
                    $nestedData['whatsapp_link'] .= '<i class="fab fa-whatsapp"></i></a>';
                    
                    // Add template ID if available
                    if ($retreat->whatsapp_template_id) {
                        $nestedData['whatsapp_link'] .= '<br><small class="text-muted">Tem.ID: ' . $retreat->whatsapp_template_id . '</small>';
                    }
                } else {
                    $nestedData['whatsapp_link'] = '<span class="text-muted">-</span>';
                }
                
                // Status badges
                $statusBadges = '';
                if ($retreat->trashed()) {
                    $statusBadges .= '<span class="badge bg-danger">Deleted</span> ';
                }
                if ($retreat->is_featured) {
                    $statusBadges .= '<span class="badge bg-warning">Featured</span> ';
                }
                $statusBadges .= $retreat->is_active 
                    ? '<span class="badge bg-success">Active</span>' 
                    : '<span class="badge bg-secondary">Inactive</span>';
                
                $nestedData['status'] = $statusBadges;
                
                // Check if retreat has ended (before today)
                $hasEnded = $retreat->end_date->toDateString() < now()->toDateString();
                
                // Actions
                $actions = '<div class="btn-group" role="group">';
                
                // If deleted, show restore button
                if ($retreat->trashed()) {
                    if (Auth::user()->can('delete-retreats')) {
                        $actions .= '<form action="' . route('admin.retreats.restore', $retreat->id) . '" method="POST" class="d-inline">';
                        $actions .= csrf_field();
                        $actions .= '<button type="submit" class="btn btn-success btn-sm" title="Restore" ';
                        $actions .= 'onclick="return confirm(\'Are you sure you want to restore this retreat?\')">';
                        $actions .= '<i class="fas fa-undo"></i></button></form>';
                    }
                } else {
                    // Normal actions for non-deleted retreats
                    $actions .= '<a href="' . route('admin.retreats.show', $retreat) . '" class="btn btn-info btn-sm" title="View">';
                    $actions .= '<i class="fas fa-eye"></i></a> ';
                    
                    // Edit button - check permission
                    if (Auth::user()->can('edit-retreats')) {
                        if ($hasEnded) {
                            // Disabled Edit button for past retreats
                            $actions .= '<button class="btn btn-primary btn-sm" title="Cannot edit past retreat" disabled>';
                            $actions .= '<i class="fas fa-edit"></i></button> ';
                        } else {
                            // Active Edit button for current/future retreats
                            $actions .= '<a href="' . route('admin.retreats.edit', $retreat) . '" class="btn btn-primary btn-sm" title="Edit">';
                            $actions .= '<i class="fas fa-edit"></i></a> ';
                        }
                    }
                    
                    // Delete button - check permission
                    if (Auth::user()->can('delete-retreats')) {
                        if ($hasEnded) {
                            // Disabled Delete button for past retreats
                            $actions .= '<button class="btn btn-danger btn-sm" title="Cannot delete past retreat" disabled>';
                            $actions .= '<i class="fas fa-trash"></i></button>';
                        } else {
                            // Active Delete button for current/future retreats
                            $actions .= '<form action="' . route('admin.retreats.destroy', $retreat) . '" method="POST" class="d-inline">';
                            $actions .= csrf_field();
                            $actions .= method_field('DELETE');
                            $actions .= '<button type="submit" class="btn btn-danger btn-sm" title="Delete" ';
                            $actions .= 'onclick="return confirm(\'Are you sure you want to delete this retreat?\')">';
                            $actions .= '<i class="fas fa-trash"></i></button></form>';
                        }
                    }
                }
                
                $actions .= '</div>';
                
                $nestedData['actions'] = $actions;
                
                $data[] = $nestedData;
            }
            
            // Calculate total records with organization filtering
            $totalRecordsQuery = Retreat::query();
            if (!$isSuperAdmin && $organizationId) {
                $totalRecordsQuery->where('organization_id', $organizationId);
            }
            
            $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalRecordsQuery->count()),
                "recordsFiltered" => intval($totalData),
                "data"            => $data
            ];
            
            return response()->json($json_data);
        }
        
        return view('admin.retreats.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $criteriaOptions = \App\Models\Criteria::where('status', 1)->pluck('name', 'id');
        
        $user = auth()->user();
        $organizations = [];
        
        // Only show organization dropdown for Super Admins and users without organization_id
        if ($user->isSuperAdmin() || !$user->organization_id) {
            $organizations = \App\Models\Organization::active()->pluck('name', 'id');
        }
        
        return view('admin.retreats.create', compact('criteriaOptions', 'organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RetreatRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['title'] . ' ' . now()->format('Y-m-d'));
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();
        
        $user = auth()->user();
        
        // Handle organization_id assignment
        if ($user->organization_id) {
            // If user has organization_id, auto-assign it
            $validated['organization_id'] = $user->organization_id;
        } elseif (isset($validated['organization_id']) && !empty($validated['organization_id'])) {
            // If user is Super Admin or without organization, use selected organization
            $validated['organization_id'] = $validated['organization_id'];
        } else {
            // No organization selected (optional)
            $validated['organization_id'] = null;
        }

        $retreat = Retreat::create($validated);

        return redirect()->route('admin.retreats.index')
            ->with('success', 'Retreat created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Retreat $retreat)
    {
        $retreat->load('criteriaRelation');
        return view('admin.retreats.show', compact('retreat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Retreat $retreat)
    {
        $criteriaOptions = \App\Models\Criteria::where('status', 1)->pluck('name', 'id');
        
        $user = auth()->user();
        $organizations = [];
        
        // Only show organization dropdown for Super Admins and users without organization_id
        if ($user->isSuperAdmin() || !$user->organization_id) {
            $organizations = \App\Models\Organization::active()->pluck('name', 'id');
        }
        
        return view('admin.retreats.edit', compact('retreat', 'criteriaOptions', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RetreatRequest $request, Retreat $retreat)
    {
        $validated = $request->validated();

        $validated['updated_by'] = Auth::id();
        
        $user = auth()->user();
        
        // Handle organization_id assignment
        if ($user->organization_id) {
            // If user has organization_id, keep their organization (can't change)
            $validated['organization_id'] = $user->organization_id;
        } elseif (isset($validated['organization_id']) && !empty($validated['organization_id'])) {
            // If user is Super Admin or without organization, use selected organization
            $validated['organization_id'] = $validated['organization_id'];
        } else {
            // No organization selected (optional)
            $validated['organization_id'] = null;
        }
        
        $retreat->update($validated);

        return redirect()->route('admin.retreats.index')
            ->with('success', 'Retreat updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Retreat $retreat)
    {
        // Check if retreat has any active bookings
        if ($retreat->bookings()->where('is_active', true)->exists()) {
            return back()->with('error', 'Cannot delete retreat that has active bookings. Please cancel all bookings first.');
        }
        
        $retreat->delete();
        return redirect()->route('admin.retreats.index')
            ->with('success', 'Retreat deleted successfully');
    }

    /**
     * Restore a soft-deleted retreat.
     */
    public function restore($id)
    {
        $retreat = Retreat::withTrashed()->findOrFail($id);
        $retreat->restore();
        
        return redirect()->route('admin.retreats.index')
            ->with('success', 'Retreat restored successfully');
    }
}
