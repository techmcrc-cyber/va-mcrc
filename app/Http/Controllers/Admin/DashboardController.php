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
        // Calculate date ranges
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        
        // User Statistics with month-over-month comparison
        $totalUsers = User::count();
        $totalUsersLastMonth = User::where('created_at', '<', $currentMonth)->count();
        $userGrowth = $this->calculatePercentageChange($totalUsersLastMonth, $totalUsers);
        
        $userStats = [
            'total' => $totalUsers,
            'active' => User::where('is_active', true)->count(),
            'new_this_month' => User::where('created_at', '>=', $currentMonth)->count(),
            'new_last_month' => User::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count(),
            'growth_percentage' => $userGrowth,
            'recent' => User::with('role')->latest()->take(5)->get()
        ];

        // Retreat Statistics with month-over-month comparison
        $totalRetreats = Retreat::count();
        $totalRetreatsLastMonth = Retreat::where('created_at', '<', $currentMonth)->count();
        $retreatGrowth = $this->calculatePercentageChange($totalRetreatsLastMonth, $totalRetreats);
        
        $retreatStats = [
            'total' => $totalRetreats,
            'active' => Retreat::where('is_active', true)->count(),
            'upcoming' => Retreat::upcoming()->count(),
            'ongoing' => Retreat::ongoing()->count(),
            'featured' => Retreat::featured()->count(),
            'new_this_month' => Retreat::where('created_at', '>=', $currentMonth)->count(),
            'new_last_month' => Retreat::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count(),
            'growth_percentage' => $retreatGrowth,
        ];

        // Booking Statistics with month-over-month comparison
        $totalBookings = Booking::where('is_active', true)->count();
        $totalBookingsLastMonth = Booking::where('is_active', true)
            ->where('created_at', '<', $currentMonth)->count();
        $bookingGrowth = $this->calculatePercentageChange($totalBookingsLastMonth, $totalBookings);
        
        $bookingStats = [
            'total' => $totalBookings,
            'active' => Booking::where('is_active', true)->count(),
            'primary_participants' => Booking::where('is_active', true)
                ->where('participant_number', 1)->count(),
            'additional_participants' => Booking::where('is_active', true)
                ->where('participant_number', '>', 1)->count(),
            'new_this_month' => Booking::where('is_active', true)
                ->where('created_at', '>=', $currentMonth)->count(),
            'new_last_month' => Booking::where('is_active', true)
                ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count(),
            'growth_percentage' => $bookingGrowth,
            'recent_bookings' => Booking::with(['retreat'])
                ->where('is_active', true)
                ->latest()
                ->take(10)
                ->get()
        ];

        // Additional User Statistics (Admin Users count)
        $adminUsers = User::whereHas('role', function($query) {
            $query->where('name', 'like', '%admin%')
                  ->orWhere('is_super_admin', true);
        })->count();
        
        $adminUsersLastMonth = User::whereHas('role', function($query) {
            $query->where('name', 'like', '%admin%')
                  ->orWhere('is_super_admin', true);
        })->where('created_at', '<', $currentMonth)->count();
        
        $adminUserGrowth = $this->calculatePercentageChange($adminUsersLastMonth, $adminUsers);

        // Recent Activities (Real data from bookings and retreats)
        $activities = collect();
        
        // Add recent bookings as activities
        $recentBookings = Booking::with('retreat')
            ->where('is_active', true)
            ->where('participant_number', 1) // Only primary participants
            ->latest()
            ->take(3)
            ->get();
            
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
        $recentRetreats = Retreat::latest()->take(2)->get();
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

        // Get recent users
        $recentUsers = User::with('role')
            ->latest()
            ->take(5)
            ->get();

        // Monthly booking trends for charts
        $monthlyBookings = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyBookings[] = [
                'month' => $date->format('M Y'),
                'count' => Booking::where('is_active', true)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
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
