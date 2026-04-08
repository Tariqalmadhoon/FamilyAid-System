<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\InteractsWithCampAccess;
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
    use InteractsWithCampAccess;

    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        $canViewHouseholds = $this->currentUser()->hasManagementPermission('households.view');
        $canCreateHouseholds = $this->currentUser()->hasManagementPermission('households.create');
        $canVerifyHouseholds = $this->currentUser()->hasManagementPermission('households.verify');
        $canViewDistributions = $this->currentUser()->hasManagementPermission('distributions.view');
        $canCreateDistributions = $this->currentUser()->hasManagementPermission('distributions.create');

        $householdsQuery = $canViewHouseholds
            ? Household::query()->visibleTo($this->currentUser())
            : Household::query()->whereRaw('1 = 0');

        $distributionsQuery = $canViewDistributions
            ? Distribution::query()->visibleTo($this->currentUser())
            : Distribution::query()->whereRaw('1 = 0');

        $membersQuery = $canViewHouseholds
            ? HouseholdMember::query()->whereHas('household', function ($query) {
                $query->visibleTo($this->currentUser());
            })
            : HouseholdMember::query()->whereRaw('1 = 0');

        $incompleteCitizenRegistrations = 0;
        $unlinkedCitizenHouseholds = 0;

        if (! $this->isCampManager()) {
            $incompleteCitizenBaseQuery = User::query()
                ->where('is_staff', false)
                ->whereNull('household_id');

            $incompleteCitizenRegistrations = (clone $incompleteCitizenBaseQuery)->count();
            $unlinkedCitizenHouseholds = (clone $incompleteCitizenBaseQuery)
                ->whereExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('households')
                        ->whereColumn('households.head_national_id', 'users.national_id');
                })
                ->count();
        }

        $stats = [
            'total_households' => (clone $householdsQuery)->count(),
            'pending_households' => (clone $householdsQuery)->pending()->count(),
            'verified_households' => (clone $householdsQuery)->verified()->count(),
            'incomplete_citizen_registrations' => $incompleteCitizenRegistrations,
            'incomplete_missing_household_data' => max(0, $incompleteCitizenRegistrations - $unlinkedCitizenHouseholds),
            'incomplete_unlinked_households' => $unlinkedCitizenHouseholds,
            'total_members' => (clone $membersQuery)->count(),
            'active_programs' => AidProgram::active()->count(),
            'total_distributions' => (clone $distributionsQuery)->count(),
            'this_month_distributions' => (clone $distributionsQuery)->whereMonth('distribution_date', now()->month)
                ->whereYear('distribution_date', now()->year)
                ->count(),
        ];

        $recentHouseholds = $canViewHouseholds
            ? Household::with('region')
                ->visibleTo($this->currentUser())
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        $recentDistributions = $canViewDistributions
            ? Distribution::with(['household', 'aidProgram', 'distributor'])
                ->visibleTo($this->currentUser())
                ->latest('distribution_date')
                ->limit(5)
                ->get()
            : collect();

        $householdsByRegion = $canViewHouseholds
            ? Household::selectRaw('region_id, count(*) as count')
                ->visibleTo($this->currentUser())
                ->groupBy('region_id')
                ->with('region')
                ->get()
            : collect();

        $campStats = $canViewHouseholds
            ? Region::query()
                ->when(
                    $this->isCampManager(),
                    function ($query) {
                        $query->whereKey($this->managedRegionId());
                    },
                    function ($query) {
                        $query->allowedCamps();
                    }
                )
                ->withCount('households')
                ->orderBy('name')
                ->get()
            : collect();

        $totalCampRegistered = (int) $campStats->sum('households_count');
        $maxCampRegistered = (int) max(1, (int) $campStats->max('households_count'));

        $recentUsers = $canViewHouseholds
            ? User::query()
                ->whereHas('household', function ($query) {
                    $query->visibleTo($this->currentUser());
                })
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentHouseholds' => $recentHouseholds,
            'recentDistributions' => $recentDistributions,
            'householdsByRegion' => $householdsByRegion,
            'campStats' => $campStats,
            'totalCampRegistered' => $totalCampRegistered,
            'maxCampRegistered' => $maxCampRegistered,
            'recentUsers' => $recentUsers,
            'isCampManager' => $this->isCampManager(),
            'canViewHouseholds' => $canViewHouseholds,
            'canCreateHouseholds' => $canCreateHouseholds,
            'canVerifyHouseholds' => $canVerifyHouseholds,
            'canViewDistributions' => $canViewDistributions,
            'canCreateDistributions' => $canCreateDistributions,
        ]);
    }
}
