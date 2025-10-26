<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    /**
     * Show general settings form
     */
    public function general()
    {
        $settings = [
            'site_name' => setting('site_name', config('app.name')),
            'site_description' => setting('site_description', 'A Christian Retreat Management System'),
            'site_email' => setting('site_email', 'admin@example.com'),
            'site_phone' => setting('site_phone', ''),
            'site_address' => setting('site_address', ''),
            'timezone' => setting('timezone', config('app.timezone')),
            'date_format' => setting('date_format', 'Y-m-d'),
            'time_format' => setting('time_format', 'H:i'),
        ];

        return view('admin.settings.general', compact('settings'));
    }

    /**
     * Show email settings form
     */
    public function email()
    {
        $settings = [
            'mail_mailer' => setting('mail_mailer', 'smtp'),
            'mail_host' => setting('mail_host', ''),
            'mail_port' => setting('mail_port', '587'),
            'mail_username' => setting('mail_username', ''),
            'mail_password' => setting('mail_password', ''),
            'mail_encryption' => setting('mail_encryption', 'tls'),
            'mail_from_address' => setting('mail_from_address', 'hello@example.com'),
            'mail_from_name' => setting('mail_from_name', config('app.name')),
        ];

        return view('admin.settings.email', compact('settings'));
    }

    /**
     * Show payment settings form
     */
    public function payment()
    {
        $settings = [
            'currency' => setting('currency', 'USD'),
            'currency_symbol' => setting('currency_symbol', '$'),
            'stripe_key' => setting('stripe_key', ''),
            'stripe_secret' => setting('stripe_secret', ''),
            'paypal_client_id' => setting('paypal_client_id', ''),
            'paypal_secret' => setting('paypal_secret', ''),
            'payment_mode' => setting('payment_mode', 'sandbox'),
        ];

        return view('admin.settings.payment', compact('settings'));
    }

    /**
     * Show notification settings form
     */
    public function notification()
    {
        $settings = [
            'notifications_enabled' => setting('notifications_enabled', '1'),
            'email_notifications' => setting('email_notifications', '1'),
            'sms_notifications' => setting('sms_notifications', '0'),
            'push_notifications' => setting('push_notifications', '1'),
            'booking_confirmation' => setting('booking_confirmation', '1'),
            'payment_received' => setting('payment_received', '1'),
            'upcoming_event' => setting('upcoming_event', '1'),
        ];

        return view('admin.settings.notification', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request, $type)
    {
        $settings = $request->except(['_token', '_method']);
        
        foreach ($settings as $key => $value) {
            setting([$key => $value])->save();
        }

        // Clear settings cache
        Cache::forget('settings');

        return redirect()->back()
            ->with('success', ucfirst($type) . ' settings updated successfully!');
    }
    
    /**
     * Send a test email
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        
        try {
            Mail::raw('This is a test email from ' . config('app.name'), function($message) use ($request) {
                $message->to($request->email)
                        ->subject('Test Email from ' . config('app.name'));
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Test email failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show system settings (Super Admin only)
     */
    public function system()
    {
        // Check if user is super admin (no permission check, only role check)
        if (!auth()->user()->role || !auth()->user()->role->is_super_admin) {
            abort(403, 'Unauthorized action. Only super admins can access system settings.');
        }

        $settings = \App\Models\Setting::orderBy('key')->get();
        
        return view('admin.settings.system', compact('settings'));
    }

    /**
     * Store or update system setting
     */
    public function storeSystemSetting(Request $request)
    {
        // Check if user is super admin (no permission check, only role check)
        if (!auth()->user()->role || !auth()->user()->role->is_super_admin) {
            abort(403, 'Unauthorized action. Only super admins can access system settings.');
        }

        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'nullable',
            'type' => 'required|in:string,integer,boolean,json',
            'description' => 'nullable|string|max:500'
        ]);

        \App\Models\Setting::set(
            $request->key,
            $request->value,
            $request->type,
            $request->description
        );

        return redirect()->back()
            ->with('success', 'Setting saved successfully!');
    }

    /**
     * Delete system setting
     */
    public function deleteSystemSetting($id)
    {
        // Check if user is super admin (no permission check, only role check)
        if (!auth()->user()->role || !auth()->user()->role->is_super_admin) {
            abort(403, 'Unauthorized action. Only super admins can access system settings.');
        }

        $setting = \App\Models\Setting::findOrFail($id);
        Cache::forget("setting_{$setting->key}");
        $setting->delete();

        return redirect()->back()
            ->with('success', 'Setting deleted successfully!');
    }
}
