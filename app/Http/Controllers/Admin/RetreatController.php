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
        if ($request->ajax()) {
            $query = Retreat::query();
            
            // Handle search
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
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
            
            $retreats = $query->offset($start)
                            ->limit($limit)
                            ->get();
            
            $criteriaLabels = [
                'male_only' => 'Male Only',
                'female_only' => 'Female Only',
                'priests_only' => 'Priests Only',
                'sisters_only' => 'Sisters Only',
                'youth_only' => 'Youth Only',
                'children' => 'Children',
                'no_criteria' => 'No Criteria'
            ];
            
            $data = [];
            foreach ($retreats as $retreat) {
                $nestedData = [];
                $nestedData['title'] = $retreat->title;
                $nestedData['date'] = $retreat->start_date->format('M d, Y') . ' - ' . $retreat->end_date->format('M d, Y');
                $nestedData['end_date'] = $retreat->end_date->format('Y-m-d'); // For sorting
                $nestedData['timings'] = $retreat->timings;
                $nestedData['seats'] = $retreat->seats;
                $nestedData['criteria'] = $criteriaLabels[$retreat->criteria] ?? $retreat->criteria;
                
                // WhatsApp Channel Link
                if ($retreat->whatsapp_channel_link) {
                    $nestedData['whatsapp_link'] = '<a href="' . e($retreat->whatsapp_channel_link) . '" target="_blank" class="btn btn-success btn-sm" title="WhatsApp Channel">';
                    $nestedData['whatsapp_link'] .= '<i class="fab fa-whatsapp"></i></a>';
                } else {
                    $nestedData['whatsapp_link'] = '<span class="text-muted">-</span>';
                }
                
                $nestedData['status'] = $retreat->is_active 
                    ? '<span class="badge bg-success">Active</span>' 
                    : '<span class="badge bg-secondary">Inactive</span>';
                
                // Actions
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('admin.retreats.show', $retreat) . '" class="btn btn-info btn-sm" title="View">';
                $actions .= '<i class="fas fa-eye"></i></a> ';
                $actions .= '<a href="' . route('admin.retreats.edit', $retreat) . '" class="btn btn-primary btn-sm" title="Edit">';
                $actions .= '<i class="fas fa-edit"></i></a> ';
                $actions .= '<form action="' . route('admin.retreats.destroy', $retreat) . '" method="POST" class="d-inline">';
                $actions .= csrf_field();
                $actions .= method_field('DELETE');
                $actions .= '<button type="submit" class="btn btn-danger btn-sm" title="Delete" ';
                $actions .= 'onclick="return confirm(\'Are you sure you want to delete this retreat?\')">';
                $actions .= '<i class="fas fa-trash"></i></button></form>';
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
        
        return view('admin.retreats.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $criteriaOptions = [
            'male_only' => 'Male Only',
            'female_only' => 'Female Only',
            'priests_only' => 'Priests Only',
            'sisters_only' => 'Sisters Only',
            'youth_only' => 'Youth Only (Age 16-30)',
            'children' => 'Children (Age 15 or below)',
            'no_criteria' => 'No Criteria'
        ];
        
        return view('admin.retreats.create', compact('criteriaOptions'));
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

        $retreat = Retreat::create($validated);

        return redirect()->route('admin.retreats.index')
            ->with('success', 'Retreat created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Retreat $retreat)
    {
        return view('admin.retreats.show', compact('retreat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Retreat $retreat)
    {
        $criteriaOptions = [
            'male_only' => 'Male Only',
            'female_only' => 'Female Only',
            'priests_only' => 'Priests Only',
            'sisters_only' => 'Sisters Only',
            'youth_only' => 'Youth Only (Age 16-30)',
            'children' => 'Children (Age 15 or below)',
            'no_criteria' => 'No Criteria'
        ];
        
        return view('admin.retreats.edit', compact('retreat', 'criteriaOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RetreatRequest $request, Retreat $retreat)
    {
        $validated = $request->validated();

        $validated['updated_by'] = Auth::id();
        $retreat->update($validated);

        return redirect()->route('admin.retreats.index')
            ->with('success', 'Retreat updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Retreat $retreat)
    {
        $retreat->delete();
        return redirect()->route('admin.retreats.index')
            ->with('success', 'Retreat deleted successfully');
    }
}
