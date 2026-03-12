<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Display the support page.
     */
    public function index()
    {
        return view('admin.support.index');
    }

    /**
     * Handle support form submission.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        // For now, just return success message
        // In the future, this could send an email or create a ticket
        
        return back()->with('success', 'Your support request has been submitted successfully. We will get back to you soon.');
    }
}