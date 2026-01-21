<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $households = $query->latest()->paginate(15)->withQueryString();

        $regions = Region::with('children')->whereNull('parent_id')->get();

        return view('admin.households.index', [
            'households' => $households,
            'regions' => $regions,
            'filters' => $request->only(['search', 'status', 'region_id', 'housing_type']),
        ]);
    }

    /**
     * Show the form for creating a new household.
     */
    public function create(): View
    {
        $regions = Region::with('children')->whereNull('parent_id')->get();

        return view('admin.households.create', [
            'regions' => $regions,
            'housingTypes' => ['owned', 'rented', 'family_hosted', 'other'],
        ]);
    }

    /**
     * Store a newly created household.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'head_national_id' => ['required', 'string', 'max:20', 'unique:households,head_national_id'],
            'head_name' => ['required', 'string', 'max:255'],
            'region_id' => ['required', 'exists:regions,id'],
            'address_text' => ['nullable', 'string', 'max:500'],
            'housing_type' => ['nullable', 'in:owned,rented,family_hosted,other'],
            'primary_phone' => ['nullable', 'string', 'max:20'],
            'secondary_phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:pending,verified,suspended,rejected'],
            'notes' => ['nullable', 'string'],
        ]);

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
        $regions = Region::with('children')->whereNull('parent_id')->get();

        return view('admin.households.edit', [
            'household' => $household,
            'regions' => $regions,
            'housingTypes' => ['owned', 'rented', 'family_hosted', 'other'],
        ]);
    }

    /**
     * Update the specified household.
     */
    public function update(Request $request, Household $household): RedirectResponse
    {
        $validated = $request->validate([
            'head_national_id' => ['required', 'string', 'max:20', 'unique:households,head_national_id,' . $household->id],
            'head_name' => ['required', 'string', 'max:255'],
            'region_id' => ['required', 'exists:regions,id'],
            'address_text' => ['nullable', 'string', 'max:500'],
            'housing_type' => ['nullable', 'in:owned,rented,family_hosted,other'],
            'primary_phone' => ['nullable', 'string', 'max:20'],
            'secondary_phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:pending,verified,suspended,rejected'],
            'notes' => ['nullable', 'string'],
        ]);

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
}
