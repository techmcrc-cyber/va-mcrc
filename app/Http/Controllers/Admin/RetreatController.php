<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Retreat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RetreatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $retreats = Retreat::latest()->paginate(10);
        return view('admin.retreats.index', compact('retreats'));
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'timings' => 'required|string|max:255',
            'seats' => 'required|integer|min:0',
            'criteria' => 'required|in:male_only,female_only,priests_only,sisters_only,youth_only,children,no_criteria',
            'special_remarks' => 'nullable|string',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

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
    public function update(Request $request, Retreat $retreat)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'timings' => 'required|string|max:255',
            'seats' => 'required|integer|min:0',
            'criteria' => 'required|in:male_only,female_only,priests_only,sisters_only,youth_only,children,no_criteria',
            'special_remarks' => 'nullable|string',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

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
