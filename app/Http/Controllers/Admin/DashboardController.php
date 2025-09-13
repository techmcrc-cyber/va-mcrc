<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Member;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // User Statistics
        $userStats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'new_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
            'recent' => User::with('role')
                ->latest()
                ->take(5)
                ->get()
        ];

        // Activities Statistics (Static Data)
        $activityStats = [
            'total' => 12,
            'upcoming' => 5,
            'ongoing' => 2,
            'recent' => []
        ];

        // Event Statistics (Static Data)
        $eventStats = [
            'total' => 15,
            'upcoming' => 8,
            'completed' => 7,
            'recent' => []
        ];

        // Member Statistics (Static Data)
        $memberStats = [
            'total' => 42,
            'new_this_month' => 12,
            'active' => 38,
            'recent' => []
        ];

        // Revenue Statistics
        $revenueStats = [
            'total' => 1000,
            'this_month' => 500,
            'last_month' => 400,
            'monthly_trend' => [
                ['month' => 'Jan 2023', 'revenue' => 100],
                ['month' => 'Feb 2023', 'revenue' => 200],
                ['month' => 'Mar 2023', 'revenue' => 300],
                ['month' => 'Apr 2023', 'revenue' => 400],
                ['month' => 'May 2023', 'revenue' => 500],
                ['month' => 'Jun 2023', 'revenue' => 600],
            ]
        ];

        // Static activity data (replacing the activity log)
        $activities = collect([
            [
                'description' => 'User logged in',
                'created_at' => now(),
                'causer' => (object)['name' => 'Admin User']
            ],
            [
                'description' => 'New member registered',
                'created_at' => now()->subMinutes(30),
                'causer' => (object)['name' => 'System']
            ],
            [
                'description' => 'Event created: Sunday Service',
                'created_at' => now()->subHours(2),
                'causer' => (object)['name' => 'Admin User']
            ]
        ]);

        // Get recent users
        $recentUsers = User::with('role')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', [
            'userStats' => $userStats,
            'activityStats' => $activityStats,
            'memberStats' => $memberStats,
            'stats' => [
                'total_users' => $userStats['total'],
                'total_activities' => $activityStats['total'],
                'total_events' => $eventStats['total'],
                'total_members' => $memberStats['total'],
                'active_users' => $userStats['active'],
                'recent_users' => $recentUsers
            ]
        ]);
    }
}
