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
        $notifications = Notification::with(['retreat', 'creator'])
            ->recent()
            ->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create(): View
    {
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
