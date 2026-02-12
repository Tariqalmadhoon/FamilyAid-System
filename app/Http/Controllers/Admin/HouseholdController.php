<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HouseholdController extends Controller
{
    /**
     * Display a listing of households.
     */
    public function index(Request $request): View
    {
        $query = Household::with(['region', 'members']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('head_name', 'like', "%{$search}%")
                  ->orWhere('head_national_id', 'like', "%{$search}%")
                  ->orWhere('primary_phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by region
        if ($regionId = $request->input('region_id')) {
            $query->where('region_id', $regionId);
        }

        // Filter by housing type
        if ($housingType = $request->input('housing_type')) {
            $query->where('housing_type', $housingType);
        }

        // Health condition filters
        if ($request->input('has_war_injury')) {
            $query->hasWarInjury();
        }
        if ($request->input('has_chronic_disease')) {
            $query->hasChronicDisease();
        }
        if ($request->input('has_disability')) {
            $query->hasDisability();
        }

        // Child under 2 years filter
        if ($request->input('has_child_under_2')) {
            $query->hasChildUnderMonths(24);
        }

        // Previous Residence filters
        if ($prevGov = $request->input('previous_governorate')) {
            $query->where('previous_governorate', $prevGov);
        }
        if ($prevArea = $request->input('previous_area')) {
            $query->where('previous_area', $prevArea);
        }

        // Quick preset: Outside Al-Qarara
        if ($request->input('outside_al_qarara')) {
            $query->where(function ($q) {
                $q->where('previous_governorate', '!=', 'khan_younis')
                  ->orWhereNotIn('previous_area', ['al_qarara', 'qarara_sharqiya']);
            });
        }

        $households = $query->latest()->paginate(15)->withQueryString();

        $regions = $this->allowedCampRegionTree();

        $filters = $request->only(['search', 'status', 'region_id', 'housing_type', 'has_war_injury', 'has_chronic_disease', 'has_disability', 'has_child_under_2', 'previous_governorate', 'previous_area', 'outside_al_qarara']);

        $pendingUsers = User::where('is_staff', false)
            ->whereNull('household_id')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.households.index', [
            'households' => $households,
            'regions' => $regions,
            'filters' => $filters,
            'hasActiveFilters' => collect($filters)->filter(function ($v) {
                return $v !== null && $v !== '' && $v !== false;
            })->isNotEmpty(),
            'pendingUsers' => $pendingUsers,
            'previousGovernorates' => __('messages.previous_governorates'),
            'previousAreas' => __('messages.previous_areas'),
        ]);
    }

    /**
     * Show the form for creating a new household.
     */
    public function create(): View
    {
        $regions = $this->allowedCampRegionTree();

        return view('admin.households.create', [
            'regions' => $regions,
            'housingTypes' => ['owned', 'rented', 'family_hosted', 'other'],
            'previousGovernorates' => __('messages.previous_governorates'),
            'previousAreas' => __('messages.previous_areas'),
        ]);
    }

    /**
     * Store a newly created household.
     */
    public function store(Request $request): RedirectResponse
    {
        $allowedRegionIds = $this->allowedCampRegionIds();

        $validated = $request->validate([
            'head_national_id' => ['required', 'digits:9', 'unique:households,head_national_id'],
            'head_name' => ['required', 'string', 'max:255'],
            'region_id' => ['required', Rule::in($allowedRegionIds)],
            'address_text' => ['nullable', 'string', 'max:500'],
            'housing_type' => ['nullable', 'in:owned,rented,family_hosted,other'],
            'primary_phone' => ['nullable', 'digits:10'],
            'secondary_phone' => ['nullable', 'digits:10'],
            'status' => ['required', 'in:pending,verified,suspended,rejected'],
            'notes' => ['nullable', 'string'],
            'has_war_injury' => ['nullable', 'boolean'],
            'has_chronic_disease' => ['nullable', 'boolean'],
            'has_disability' => ['nullable', 'boolean'],
            'condition_type' => ['nullable', 'string', 'max:255'],
            'condition_notes' => ['nullable', 'string', 'max:1000'],
            'previous_governorate' => ['nullable', 'string', 'max:100'],
            'previous_area' => ['nullable', 'string', 'max:100'],
        ], [
            'region_id.in' => __('messages.onboarding_form.region_not_allowed'),
        ]);

        // Normalize checkbox values
        $validated['has_war_injury'] = $request->boolean('has_war_injury');
        $validated['has_chronic_disease'] = $request->boolean('has_chronic_disease');
        $validated['has_disability'] = $request->boolean('has_disability');

        $household = Household::create($validated);

        AuditLog::log('create', 'Household', $household->id, null, $household->toArray());

        return redirect()->route('admin.households.show', $household)
            ->with('success', 'Household created successfully!');
    }

    /**
     * Display the specified household.
     */
    public function show(Household $household): View
    {
        $household->load(['region', 'members', 'distributions.aidProgram', 'user']);

        return view('admin.households.show', [
            'household' => $household,
        ]);
    }

    /**
     * Show the form for editing the specified household.
     */
    public function edit(Household $household): View
    {
        $regions = $this->allowedCampRegionTree();

        return view('admin.households.edit', [
            'household' => $household,
            'regions' => $regions,
            'housingTypes' => ['owned', 'rented', 'family_hosted', 'other'],
            'previousGovernorates' => __('messages.previous_governorates'),
            'previousAreas' => __('messages.previous_areas'),
        ]);
    }

    /**
     * Update the specified household.
     */
    public function update(Request $request, Household $household): RedirectResponse
    {
        $allowedRegionIds = $this->allowedCampRegionIds();

        $validated = $request->validate([
            'head_national_id' => ['required', 'digits:9', 'unique:households,head_national_id,' . $household->id],
            'head_name' => ['required', 'string', 'max:255'],
            'region_id' => ['required', Rule::in($allowedRegionIds)],
            'address_text' => ['nullable', 'string', 'max:500'],
            'housing_type' => ['nullable', 'in:owned,rented,family_hosted,other'],
            'primary_phone' => ['nullable', 'digits:10'],
            'secondary_phone' => ['nullable', 'digits:10'],
            'status' => ['required', 'in:pending,verified,suspended,rejected'],
            'notes' => ['nullable', 'string'],
            'has_war_injury' => ['nullable', 'boolean'],
            'has_chronic_disease' => ['nullable', 'boolean'],
            'has_disability' => ['nullable', 'boolean'],
            'condition_type' => ['nullable', 'string', 'max:255'],
            'condition_notes' => ['nullable', 'string', 'max:1000'],
            'previous_governorate' => ['nullable', 'string', 'max:100'],
            'previous_area' => ['nullable', 'string', 'max:100'],
        ], [
            'region_id.in' => __('messages.onboarding_form.region_not_allowed'),
        ]);

        // Normalize checkbox values
        $validated['has_war_injury'] = $request->boolean('has_war_injury');
        $validated['has_chronic_disease'] = $request->boolean('has_chronic_disease');
        $validated['has_disability'] = $request->boolean('has_disability');

        $before = $household->toArray();
        $household->update($validated);

        AuditLog::log('update', 'Household', $household->id, $before, $household->fresh()->toArray());

        return redirect()->route('admin.households.show', $household)
            ->with('success', 'Household updated successfully!');
    }

    /**
     * Remove the specified household.
     */
    public function destroy(Household $household): RedirectResponse
    {
        $before = $household->toArray();
        $householdId = $household->id;
        
        $household->members()->delete();
        $household->delete();

        AuditLog::log('delete', 'Household', $householdId, $before, null);

        return redirect()->route('admin.households.index')
            ->with('success', 'Household deleted successfully!');
    }

    /**
     * Quick verify a household.
     */
    public function verify(Household $household): RedirectResponse
    {
        $before = $household->toArray();
        $household->update(['status' => 'verified']);

        AuditLog::log('verify', 'Household', $household->id, $before, $household->fresh()->toArray());

        return back()->with('success', 'Household verified successfully!');
    }

    /**
     * Bulk delete households that are outside Al-Qarara.
     * Safety: every selected ID must match the outside-Al-Qarara condition.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:households,id'],
        ]);

        $households = Household::whereIn('id', $request->input('ids'))->get();

        // Safety gate: verify every household is Outside Al-Qarara
        foreach ($households as $household) {
            $isOutside = $household->previous_governorate !== 'khan_younis'
                || !in_array($household->previous_area, ['al_qarara', 'qarara_sharqiya']);

            if (!$isOutside) {
                return back()->with('error', __('messages.households_admin.bulk_delete_invalid'));
            }
        }

        // All passed — delete each with audit log
        $count = 0;
        foreach ($households as $household) {
            $before = $household->toArray();
            $householdId = $household->id;

            $household->members()->delete();
            $household->delete();

            AuditLog::log('bulk_delete', 'Household', $householdId, $before, null);
            $count++;
        }

        return redirect()->route('admin.households.index')
            ->with('success', __('messages.households_admin.bulk_delete_success', ['count' => $count]));
    }

    private function allowedCampRegionTree()
    {
        return Region::query()
            ->with(['children' => function ($query) {
                $query->allowedCamps();
            }])
            ->whereNull('parent_id')
            ->whereHas('children', function ($query) {
                $query->allowedCamps();
            })
            ->get();
    }

    private function allowedCampRegionIds(): array
    {
        return Region::query()
            ->allowedCamps()
            ->pluck('id')
            ->all();
    }
}
