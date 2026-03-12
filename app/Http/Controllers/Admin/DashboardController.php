<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Retreat;
use App\Models\Booking;
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
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $organizationId = $user->organization_id;
        
        // Calculate date ranges
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        
        // User Statistics with month-over-month comparison
        $userQuery = User::query();
        // Only filter by organization if user has organization_id (not super admin or users without org)
        if (!$isSuperAdmin && $organizationId) {
            $userQuery->where('organization_id', $organizationId);
        }
        
        $totalUsers = $userQuery->count();
        $totalUsersLastMonth = (clone $userQuery)->where('created_at', '<', $currentMonth)->count();
        $userGrowth = $this->calculatePercentageChange($totalUsersLastMonth, $totalUsers);
        
        $userStats = [
            'total' => $totalUsers,
            'active' => (clone $userQuery)->where('is_active', true)->count(),
            'new_this_month' => (clone $userQuery)->where('created_at', '>=', $currentMonth)->count(),
            'new_last_month' => (clone $userQuery)->whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count(),
            'growth_percentage' => $userGrowth,
            'recent' => (clone $userQuery)->with('role')->latest()->take(5)->get()
        ];

        // Retreat Statistics with month-over-month comparison
        $retreatQuery = Retreat::query();
        // Only filter by organization if user has organization_id (not super admin or users without org)
        if (!$isSuperAdmin && $organizationId) {
            $retreatQuery->where('organization_id', $organizationId);
        }
        
        $totalRetreats = $retreatQuery->count();
        $totalRetreatsLastMonth = (clone $retreatQuery)->where('created_at', '<', $currentMonth)->count();
        $retreatGrowth = $this->calculatePercentageChange($totalRetreatsLastMonth, $totalRetreats);
        
        $retreatStats = [
            'total' => $totalRetreats,
            'active' => (clone $retreatQuery)->where('is_active', true)->count(),
            'upcoming' => (clone $retreatQuery)->upcoming()->count(),
            'ongoing' => (clone $retreatQuery)->ongoing()->count(),
            'featured' => (clone $retreatQuery)->featured()->count(),
            'new_this_month' => (clone $retreatQuery)->where('created_at', '>=', $currentMonth)->count(),
            'new_last_month' => (clone $retreatQuery)->whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count(),
            'growth_percentage' => $retreatGrowth,
        ];

        // Booking Statistics with month-over-month comparison
        $bookingQuery = Booking::where('is_active', true);
        // Only filter by organization if user has organization_id (not super admin or users without org)
        if (!$isSuperAdmin && $organizationId) {
            $bookingQuery->where('organization_id', $organizationId);
        }
        
        $totalBookings = $bookingQuery->count();
        $totalBookingsLastMonth = (clone $bookingQuery)->where('created_at', '<', $currentMonth)->count();
        $bookingGrowth = $this->calculatePercentageChange($totalBookingsLastMonth, $totalBookings);
        
        $bookingStats = [
            'total' => $totalBookings,
            'active' => (clone $bookingQuery)->count(),
            'primary_participants' => (clone $bookingQuery)->where('participant_number', 1)->count(),
            'additional_participants' => (clone $bookingQuery)->where('participant_number', '>', 1)->count(),
            'new_this_month' => (clone $bookingQuery)->where('created_at', '>=', $currentMonth)->count(),
            'new_last_month' => (clone $bookingQuery)->whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count(),
            'growth_percentage' => $bookingGrowth,
            'recent_bookings' => (clone $bookingQuery)->with(['retreat'])
                ->latest()
                ->take(10)
                ->get()
        ];

        // Additional User Statistics (Admin Users count) - For Super Admins and users without organization
        $adminUsers = 0;
        $adminUserGrowth = 0;
        
        if ($isSuperAdmin || !$organizationId) {
            $adminUsers = User::whereHas('role', function($query) {
                $query->where('name', 'like', '%admin%')
                      ->orWhere('is_super_admin', true);
            })->count();
            
            $adminUsersLastMonth = User::whereHas('role', function($query) {
                $query->where('name', 'like', '%admin%')
                      ->orWhere('is_super_admin', true);
            })->where('created_at', '<', $currentMonth)->count();
            
            $adminUserGrowth = $this->calculatePercentageChange($adminUsersLastMonth, $adminUsers);
        }

        // Recent Activities (Real data from bookings and retreats)
        $activities = collect();
        
        // Add recent bookings as activities
        $recentBookingsQuery = Booking::with('retreat')
            ->where('is_active', true)
            ->where('participant_number', 1); // Only primary participants
            
        // Only filter by organization if user has organization_id (not super admin or users without org)
        if (!$isSuperAdmin && $organizationId) {
            $recentBookingsQuery->where('organization_id', $organizationId);
        }
        
        $recentBookings = $recentBookingsQuery->latest()->take(3)->get();
            
        foreach ($recentBookings as $booking) {
            $activities->push([
                'type' => 'booking',
                'description' => "New booking for {$booking->retreat->title}",
                'user' => $booking->firstname . ' ' . $booking->lastname,
                'created_at' => $booking->created_at,
                'icon' => 'fas fa-calendar-check',
                'color' => 'success'
            ]);
        }
        
        // Add recent retreats as activities
        $recentRetreatsQuery = Retreat::query();
        // Only filter by organization if user has organization_id (not super admin or users without org)
        if (!$isSuperAdmin && $organizationId) {
            $recentRetreatsQuery->where('organization_id', $organizationId);
        }
        
        $recentRetreats = $recentRetreatsQuery->latest()->take(2)->get();
        foreach ($recentRetreats as $retreat) {
            $activities->push([
                'type' => 'retreat',
                'description' => "New retreat '{$retreat->title}' created",
                'user' => $retreat->creator->name ?? 'System',
                'created_at' => $retreat->created_at,
                'icon' => 'fas fa-plus-circle',
                'color' => 'primary'
            ]);
        }
        
        // Sort activities by creation date
        $activities = $activities->sortByDesc('created_at')->take(5);

        // Get recent users (scoped by organization for users with organization_id)
        $recentUsersQuery = User::with('role');
        // Only filter by organization if user has organization_id (not super admin or users without org)
        if (!$isSuperAdmin && $organizationId) {
            $recentUsersQuery->where('organization_id', $organizationId);
        }
        $recentUsers = $recentUsersQuery->latest()->take(5)->get();

        // Monthly booking trends for charts
        $monthlyBookings = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyBookingQuery = Booking::where('is_active', true)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
                
            // Only filter by organization if user has organization_id (not super admin or users without org)
            if (!$isSuperAdmin && $organizationId) {
                $monthlyBookingQuery->where('organization_id', $organizationId);
            }
            
            $monthlyBookings[] = [
                'month' => $date->format('M Y'),
                'count' => $monthlyBookingQuery->count()
            ];
        }

        return view('admin.dashboard.index', [
            'userStats' => $userStats,
            'retreatStats' => $retreatStats,
            'bookingStats' => $bookingStats,
            'adminUsers' => $adminUsers,
            'adminUserGrowth' => $adminUserGrowth,
            'activities' => $activities,
            'monthlyBookings' => $monthlyBookings,
            'isSuperAdmin' => $isSuperAdmin,
            'stats' => [
                'total_users' => $userStats['total'],
                'total_retreats' => $retreatStats['total'],
                'total_bookings' => $bookingStats['total'],
                'total_admin_users' => $adminUsers,
                'user_growth' => $userStats['growth_percentage'],
                'retreat_growth' => $retreatStats['growth_percentage'],
                'booking_growth' => $bookingStats['growth_percentage'],
                'admin_user_growth' => $adminUserGrowth,
                'recent_users' => $recentUsers
            ]
        ]);
    }
    
    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        
        $change = (($newValue - $oldValue) / $oldValue) * 100;
        return round($change, 1);
    }
}
