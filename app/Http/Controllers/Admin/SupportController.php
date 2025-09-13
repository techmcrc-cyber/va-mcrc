<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportTicket;

class SupportController extends Controller
{
    /**
     * Show the support page
     */
    public function index()
    {
        $categories = [
            'general' => 'General Inquiry',
            'bug' => 'Report a Bug',
            'feature' => 'Feature Request',
            'billing' => 'Billing Issue',
            'other' => 'Other'
        ];
        
        $priorities = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical'
        ];
        
        return view('admin.support.index', compact('categories', 'priorities'));
    }
    
    /**
     * Handle support form submission
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'category' => 'required|in:general,bug,feature,billing,other',
            'priority' => 'required|in:low,medium,high,critical',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,txt',
        ]);
        
        try {
            // Get admin email from settings or config
            $adminEmail = setting('admin_email', config('mail.from.address'));
            
            // Send email to admin
            Mail::to($adminEmail)->send(new SupportTicket($validated));
            
            // Handle file uploads if any
            if ($request->hasFile('attachments')) {
                $attachments = [];
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('support/attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime' => $file->getMimeType(),
                    ];
                }
                // Save attachments info to database or storage as needed
                // Ticket::create([...$validated, 'attachments' => $attachments]);
            }
            
            return redirect()->back()->with('success', 'Your support ticket has been submitted successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Support ticket submission failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit support ticket. Please try again later.');
        }
    }
}
