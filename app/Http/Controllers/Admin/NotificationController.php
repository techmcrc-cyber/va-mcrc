<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Models\Notification;
use App\Models\Retreat;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of notifications.
     */
    public function index(): View
    {
        $this->authorize('view-notifications');
        $perPage = request('per_page', 15);
        
        $notifications = Notification::with(['retreat', 'creator'])
            ->recent()
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Display the specified notification.
     */
    public function show(Notification $notification): View
    {
        $this->authorize('view-notifications');
        $notification->load(['retreat', 'creator']);
        
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        $this->authorize('delete-notifications');
        try {
            $notification->delete();

            return redirect()
                ->route('admin.notifications.index')
                ->with('success', 'Notification deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete notification: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create(): View
    {
        $this->authorize('create-notifications');
        $activeRetreats = Retreat::active()
            ->orderBy('start_date', 'desc')
            ->get();

        return view('admin.notifications.create', compact('activeRetreats'));
    }

    /**
     * Store a newly created notification in storage.
     */
    public function store(StoreNotificationRequest $request): RedirectResponse
    {
        $this->authorize('create-notifications');
        try {
            $notification = $this->notificationService->createNotification($request->validated());
            
            $this->notificationService->dispatchNotification($notification);

            return redirect()
                ->route('admin.notifications.index')
                ->with('success', 'Notification has been queued and will be sent shortly.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create notification: ' . $e->getMessage());
        }
    }
}
