<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AidProgram;
use App\Models\Distribution;
use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\Region;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_households' => Household::count(),
            'pending_households' => Household::pending()->count(),
            'verified_households' => Household::verified()->count(),
            'total_members' => HouseholdMember::count(),
            'active_programs' => AidProgram::active()->count(),
            'total_distributions' => Distribution::count(),
            'this_month_distributions' => Distribution::whereMonth('distribution_date', now()->month)
                ->whereYear('distribution_date', now()->year)
                ->count(),
        ];

        $recentHouseholds = Household::with('region')
            ->latest()
            ->limit(5)
            ->get();

        $recentDistributions = Distribution::with(['household', 'aidProgram', 'distributor'])
            ->latest('distribution_date')
            ->limit(5)
            ->get();

        $householdsByRegion = Household::selectRaw('region_id, count(*) as count')
            ->groupBy('region_id')
            ->with('region')
            ->get();

        $campStats = Region::query()
            ->allowedCamps()
            ->withCount('households')
            ->orderBy('name')
            ->get();

        $totalCampRegistered = (int) $campStats->sum('households_count');
        $maxCampRegistered = (int) max(1, (int) $campStats->max('households_count'));

        $recentUsers = User::latest()->limit(5)->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentHouseholds' => $recentHouseholds,
            'recentDistributions' => $recentDistributions,
            'householdsByRegion' => $householdsByRegion,
            'campStats' => $campStats,
            'totalCampRegistered' => $totalCampRegistered,
            'maxCampRegistered' => $maxCampRegistered,
            'recentUsers' => $recentUsers,
        ]);
    }
}
