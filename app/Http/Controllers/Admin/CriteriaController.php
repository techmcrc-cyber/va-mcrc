<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criteria;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('view-criteria');
        
        if ($request->ajax()) {
            $query = Criteria::query();
            
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where('name', 'like', "%{$search}%");
            }
            
            if ($request->has('order')) {
                $column = $request->input('order.0.column');
                $dir = $request->input('order.0.dir');
                $columns = ['name', 'gender', 'min_age', 'max_age', 'married', 'vocation', 'status'];
                
                if (isset($columns[$column])) {
                    $query->orderBy($columns[$column], $dir);
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            $totalData = $query->count();
            $limit = $request->input('length', 25);
            $start = $request->input('start', 0);
            
            $criteria = $query->offset($start)->limit($limit)->get();
            
            $data = [];
            foreach ($criteria as $criterion) {
                $nestedData = [];
                $nestedData['name'] = $criterion->name;
                $nestedData['gender'] = $criterion->gender ? ucfirst($criterion->gender) : '-';
                $nestedData['age_range'] = ($criterion->min_age || $criterion->max_age) 
                    ? ($criterion->min_age ?? 'Any') . ' - ' . ($criterion->max_age ?? 'Any')
                    : '-';
                $nestedData['married'] = $criterion->married ? 'Yes' : '-';
                $nestedData['vocation'] = $criterion->vocation ? str_replace('_', ' ', ucwords($criterion->vocation, '_')) : '-';
                $nestedData['status'] = $criterion->status 
                    ? '<span class="badge bg-success">Active</span>' 
                    : '<span class="badge bg-secondary">Inactive</span>';
                
                $actions = '<div class="btn-group" role="group">';
                
                // Edit button - check permission
                if (auth()->user()->can('edit-criteria')) {
                    $actions .= '<a href="' . route('admin.criteria.edit', $criterion) . '" class="btn btn-primary btn-sm" title="Edit">';
                    $actions .= '<i class="fas fa-edit"></i></a> ';
                }
                
                // Delete button - check permission
                if (auth()->user()->can('delete-criteria')) {
                    $actions .= '<form action="' . route('admin.criteria.destroy', $criterion) . '" method="POST" class="d-inline">';
                    $actions .= csrf_field();
                    $actions .= method_field('DELETE');
                    $actions .= '<button type="submit" class="btn btn-danger btn-sm" title="Delete" ';
                    $actions .= 'onclick="return confirm(\'Are you sure you want to delete this criteria?\')">';
                    $actions .= '<i class="fas fa-trash"></i></button></form>';
                }
                
                $actions .= '</div>';
                
                $nestedData['actions'] = $actions;
                $data[] = $nestedData;
            }
            
            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalData),
                "data" => $data
            ]);
        }
        
        return view('admin.criteria.index');
    }

    public function create()
    {
        $this->authorize('create-criteria');
        
        return view('admin.criteria.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create-criteria');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|min:0|gte:min_age',
            'married' => 'nullable|in:yes',
            'vocation' => 'nullable|in:priest_only,sisters_only',
            'status' => 'boolean'
        ]);

        Criteria::create($validated);

        return redirect()->route('admin.criteria.index')
            ->with('success', 'Criteria created successfully.');
    }

    public function edit(Criteria $criterion)
    {
        $this->authorize('edit-criteria');
        
        return view('admin.criteria.edit', compact('criterion'));
    }

    public function update(Request $request, Criteria $criterion)
    {
        $this->authorize('edit-criteria');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|min:0|gte:min_age',
            'married' => 'nullable|in:yes',
            'vocation' => 'nullable|in:priest_only,sisters_only',
            'status' => 'boolean'
        ]);

        $criterion->update($validated);

        return redirect()->route('admin.criteria.index')
            ->with('success', 'Criteria updated successfully');
    }

    public function destroy(Criteria $criterion)
    {
        $this->authorize('delete-criteria');
        
        // Check if criteria is being used by any retreats
        if ($criterion->retreats()->exists()) {
            return back()->with('error', 'Cannot delete criteria that is assigned to retreats. Please remove it from all retreats first.');
        }
        
        $criterion->delete();
        return redirect()->route('admin.criteria.index')
            ->with('success', 'Criteria deleted successfully');
    }
}
