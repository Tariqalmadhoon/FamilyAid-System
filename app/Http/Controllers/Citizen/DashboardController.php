<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Distribution;
use App\Models\Household;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
            'spouse_full_name' => trim((string) $request->input('spouse_full_name')),
            'spouse_national_id' => preg_replace('/\D+/', '', (string) $request->input('spouse_national_id', '')),
            'spouse_has_war_injury' => $request->boolean('spouse_has_war_injury'),
            'spouse_has_chronic_disease' => $request->boolean('spouse_has_chronic_disease'),
            'spouse_has_disability' => $request->boolean('spouse_has_disability'),
            'spouse_condition_type' => trim((string) $request->input('spouse_condition_type')),
            'payment_account_number' => preg_replace('/\D+/', '', (string) $request->input('payment_account_number', '')),
            'payment_account_holder_name' => trim((string) $request->input('payment_account_holder_name')),
        ]);

        $validator = Validator::make($request->all(), [
            'region_id' => ['required', Rule::in($allowedRegionIds)],
            'spouse_full_name' => ['required', 'string', 'max:255'],
            'spouse_national_id' => [
                'required',
                'digits:9',
                Rule::notIn([$user->national_id]),
                Rule::unique('households', 'spouse_national_id')->ignore($user->household_id),
            ],
            'spouse_birth_date' => ['required', 'date', 'before:today'],
            'spouse_has_war_injury' => ['nullable', 'boolean'],
            'spouse_has_chronic_disease' => ['nullable', 'boolean'],
            'spouse_has_disability' => ['nullable', 'boolean'],
            'spouse_condition_type' => ['nullable', 'string', 'max:255'],
            'spouse_health_notes' => ['nullable', 'string', 'max:1000'],
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

        $validator->after(function ($validator) use ($request) {
            $spouseHasHealthFlag = $request->boolean('spouse_has_war_injury')
                || $request->boolean('spouse_has_chronic_disease')
                || $request->boolean('spouse_has_disability');

            if ($spouseHasHealthFlag && blank($request->input('spouse_condition_type'))) {
                $validator->errors()->add('spouse_condition_type', __('validation.required', ['attribute' => __('messages.onboarding_form.spouse_condition_type')]));
            }
        });

        $validated = $validator->validate();

        $spouseHasHealthFlag = $request->boolean('spouse_has_war_injury')
            || $request->boolean('spouse_has_chronic_disease')
            || $request->boolean('spouse_has_disability');

        $household = Household::find($user->household_id);
        $before = $household->toArray();

        $household->update(array_merge($validated, [
            'has_war_injury' => $request->boolean('has_war_injury'),
            'has_chronic_disease' => $request->boolean('has_chronic_disease'),
            'has_disability' => $request->boolean('has_disability'),
            'spouse_has_war_injury' => $request->boolean('spouse_has_war_injury'),
            'spouse_has_chronic_disease' => $request->boolean('spouse_has_chronic_disease'),
            'spouse_has_disability' => $request->boolean('spouse_has_disability'),
            'spouse_condition_type' => $spouseHasHealthFlag ? ($validated['spouse_condition_type'] ?? null) : null,
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
