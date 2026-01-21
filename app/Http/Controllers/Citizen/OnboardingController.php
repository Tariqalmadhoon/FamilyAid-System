<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding wizard.
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        // If already has household, redirect to dashboard
        if ($user->household_id) {
            return redirect()->route('citizen.dashboard');
        }

        $regions = Region::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->get();

        return view('citizen.onboarding', [
            'regions' => $regions,
            'housingTypes' => [
                'owned' => 'Owned',
                'rented' => 'Rented',
                'family_hosted' => 'Family Hosted',
                'other' => 'Other',
            ],
            'relations' => [
                'spouse' => 'Spouse',
                'son' => 'Son',
                'daughter' => 'Daughter',
                'father' => 'Father',
                'mother' => 'Mother',
                'brother' => 'Brother',
                'sister' => 'Sister',
                'grandfather' => 'Grandfather',
                'grandmother' => 'Grandmother',
                'grandson' => 'Grandson',
                'granddaughter' => 'Granddaughter',
                'uncle' => 'Uncle',
                'aunt' => 'Aunt',
                'nephew' => 'Nephew',
                'niece' => 'Niece',
                'cousin' => 'Cousin',
                'other' => 'Other Relative',
            ],
        ]);
    }

    /**
     * Store the household from onboarding wizard.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Prevent duplicate submissions
        if ($user->household_id) {
            return redirect()->route('citizen.dashboard')
                ->with('info', 'Your household is already registered.');
        }

        $validated = $request->validate([
            // Step 1: Region & Address
            'region_id' => ['required', 'exists:regions,id'],
            'address_text' => ['required', 'string', 'max:500'],
            
            // Step 2: Housing & Contact
            'housing_type' => ['required', 'in:owned,rented,family_hosted,other'],
            'primary_phone' => ['required', 'string', 'max:20'],
            'secondary_phone' => ['nullable', 'string', 'max:20'],
            
            // Step 3: Members (optional array)
            'members' => ['nullable', 'array', 'max:20'],
            'members.*.full_name' => ['required_with:members', 'string', 'max:255'],
            'members.*.national_id' => ['nullable', 'string', 'max:20'],
            'members.*.relation_to_head' => ['required_with:members', 'string', 'max:50'],
            'members.*.gender' => ['nullable', 'in:male,female'],
            'members.*.birth_date' => ['nullable', 'date', 'before:today'],
        ]);

        DB::transaction(function () use ($user, $validated) {
            // Create household
            $household = Household::create([
                'head_national_id' => $user->national_id,
                'head_name' => $user->name,
                'region_id' => $validated['region_id'],
                'address_text' => $validated['address_text'],
                'housing_type' => $validated['housing_type'],
                'primary_phone' => $validated['primary_phone'],
                'secondary_phone' => $validated['secondary_phone'] ?? null,
                'status' => 'pending', // Needs verification
            ]);

            // Create members if provided
            if (!empty($validated['members'])) {
                foreach ($validated['members'] as $memberData) {
                    if (!empty($memberData['full_name'])) {
                        HouseholdMember::create([
                            'household_id' => $household->id,
                            'full_name' => $memberData['full_name'],
                            'national_id' => $memberData['national_id'] ?? null,
                            'relation_to_head' => $memberData['relation_to_head'],
                            'gender' => $memberData['gender'] ?? null,
                            'birth_date' => $memberData['birth_date'] ?? null,
                        ]);
                    }
                }
            }

            // Link user to household
            $user->update(['household_id' => $household->id]);

            // Audit log
            AuditLog::log(
                'create',
                'Household',
                $household->id,
                null,
                $household->toArray()
            );
        });

        return redirect()->route('citizen.dashboard')
            ->with('success', 'Your household has been registered successfully! It is pending verification.');
    }
}
