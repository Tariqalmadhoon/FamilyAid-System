<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Distribution;
use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the citizen dashboard.
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        // If no household, redirect to onboarding
        if (!$user->household_id) {
            return redirect()->route('citizen.onboarding');
        }

        $household = Household::with(['region', 'members', 'latestDistribution.aidProgram'])
            ->find($user->household_id);

        $distributions = Distribution::with('aidProgram')
            ->where('household_id', $user->household_id)
            ->orderByDesc('distribution_date')
            ->limit(10)
            ->get();

        $lastDistribution = $distributions->first();

        return view('citizen.dashboard', [
            'household' => $household,
            'distributions' => $distributions,
            'lastDistribution' => $lastDistribution,
        ]);
    }

    /**
     * Show household edit form.
     */
    public function edit(): View|RedirectResponse
    {
        $user = auth()->user();

        if (!$user->household_id) {
            return redirect()->route('citizen.onboarding');
        }

        $household = Household::with(['region', 'members'])
            ->find($user->household_id);

        $regions = Region::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->get();

        return view('citizen.household.edit', [
            'household' => $household,
            'regions' => $regions,
            'housingTypes' => [
                'owned' => 'Owned',
                'rented' => 'Rented',
                'family_hosted' => 'Family Hosted',
                'other' => 'Other',
            ],
        ]);
    }

    /**
     * Update household information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->household_id) {
            return redirect()->route('citizen.onboarding');
        }

        $validated = $request->validate([
            'region_id' => ['required', 'exists:regions,id'],
            'address_text' => ['required', 'string', 'max:500'],
            'housing_type' => ['required', 'in:owned,rented,family_hosted,other'],
            'primary_phone' => ['required', 'string', 'max:20'],
            'secondary_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $household = Household::find($user->household_id);
        $before = $household->toArray();

        $household->update($validated);

        AuditLog::log(
            'update',
            'Household',
            $household->id,
            $before,
            $household->fresh()->toArray()
        );

        return back()->with('success', 'Household information updated successfully!');
    }
}
