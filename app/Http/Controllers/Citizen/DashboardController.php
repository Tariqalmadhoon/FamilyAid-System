<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Distribution;
use App\Models\Household;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        $regions = Region::query()
            ->select(['id', 'name'])
            ->allowedCamps()
            ->get();

        $regionOrder = array_flip(Region::ALLOWED_CAMP_REGION_NAMES);
        $regions = $regions
            ->sortBy(static fn (Region $region) => $regionOrder[$region->name] ?? PHP_INT_MAX)
            ->values();

        return view('citizen.household.edit', [
            'household' => $household,
            'regions' => $regions,
            'housingTypes' => [
                'owned' => __('messages.housing_types.owned'),
                'rented' => __('messages.housing_types.rented'),
                'family_hosted' => __('messages.housing_types.family_hosted'),
                'other' => __('messages.housing_types.other'),
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

        $allowedRegionIds = Region::query()
            ->allowedCamps()
            ->pluck('id')
            ->all();

        $request->merge([
            'payment_account_number' => preg_replace('/\D+/', '', (string) $request->input('payment_account_number', '')),
            'payment_account_holder_name' => trim((string) $request->input('payment_account_holder_name')),
        ]);

        $validated = $request->validate([
            'region_id' => ['required', Rule::in($allowedRegionIds)],
            'address_text' => ['required', 'string', 'max:500'],
            'payment_account_type' => ['required', 'in:wallet,bank'],
            'payment_account_number' => ['required', 'digits_between:6,30'],
            'payment_account_holder_name' => ['required', 'string', 'max:255'],
            'housing_type' => ['required', 'in:owned,rented,family_hosted,other'],
            'primary_phone' => ['required', 'digits:10'],
            'secondary_phone' => ['nullable', 'digits:10'],
            'has_war_injury' => ['nullable', 'boolean'],
            'has_chronic_disease' => ['nullable', 'boolean'],
            'has_disability' => ['nullable', 'boolean'],
            'condition_type' => ['nullable', 'string', 'max:255'],
            'condition_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'region_id.in' => __('messages.onboarding_form.region_not_allowed'),
        ]);

        $household = Household::find($user->household_id);
        $before = $household->toArray();

        $household->update(array_merge($validated, [
            'has_war_injury' => $request->boolean('has_war_injury'),
            'has_chronic_disease' => $request->boolean('has_chronic_disease'),
            'has_disability' => $request->boolean('has_disability'),
        ]));

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
