<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    private const ALLOWED_MEMBER_RELATION = 'son';

    private const ALLOWED_ONBOARDING_REGION_CODES = [
        'مخيم الابرار(الغفران)' => 'CAMP-ABRAR-GHFRAN',
        'مخيم الامام مالك بن انس' => 'CAMP-IMAM-MALIK',
        'مخيم ام القرى' => 'CAMP-UMM-ALQURA',
        'مخيم عثمان بن عفان' => 'CAMP-UTHMAN-AFFAN',
        'مخيم الايمان' => 'CAMP-IMAN',
        'مخيم الصمود' => 'CAMP-SUMUD',
        'مخيم النور' => 'CAMP-NOOR',
        'مخيم المسمكة' => 'CAMP-MASMAKA',
        'مخيم الصابرين' => 'CAMP-SABIREEN',
    ];

    private const ALLOWED_ONBOARDING_PARENT_NAME = 'المخيمات';
    private const ALLOWED_ONBOARDING_PARENT_CODE = 'CAMP-REGIONS';

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

        // Try to preload any existing household data (orphaned / partially saved)
        $existingHousehold = Household::with('members')
            ->where('head_national_id', $user->national_id)
            ->first();

        $regions = $this->allowedOnboardingRegions();

        $prefill = [
            'region_id' => old('region_id', $existingHousehold->region_id ?? ''),
            'address_text' => old('address_text', $existingHousehold->address_text ?? ''),
            'previous_governorate' => old('previous_governorate', $existingHousehold->previous_governorate ?? ''),
            'previous_area' => old('previous_area', $existingHousehold->previous_area ?? ''),
            'payment_account_type' => old('payment_account_type', $existingHousehold->payment_account_type ?? ''),
            'payment_account_number' => old('payment_account_number', $existingHousehold->payment_account_number ?? ''),
            'payment_account_holder_name' => old('payment_account_holder_name', $existingHousehold->payment_account_holder_name ?? ''),
            'housing_type' => old('housing_type', $existingHousehold->housing_type ?? ''),
            'primary_phone' => old('primary_phone', $existingHousehold->primary_phone ?? ''),
            'secondary_phone' => old('secondary_phone', $existingHousehold->secondary_phone ?? ''),
            'has_war_injury' => old('has_war_injury', $existingHousehold->has_war_injury ?? false),
            'has_chronic_disease' => old('has_chronic_disease', $existingHousehold->has_chronic_disease ?? false),
            'has_disability' => old('has_disability', $existingHousehold->has_disability ?? false),
            'condition_type' => old('condition_type', $existingHousehold->condition_type ?? ''),
            'condition_notes' => old('condition_notes', $existingHousehold->condition_notes ?? ''),
            'members' => old('members', $existingHousehold?->members?->map(function ($member) {
                return [
                    'full_name' => $member->full_name,
                    'national_id' => $member->national_id,
                    'relation_to_head' => $member->relation_to_head,
                    'gender' => $member->gender,
                    'birth_date' => optional($member->birth_date)->toDateString(),
                    'has_war_injury' => $member->has_war_injury,
                    'has_chronic_disease' => $member->has_chronic_disease,
                    'has_disability' => $member->has_disability,
                    'condition_type' => $member->condition_type,
                    'health_notes' => $member->health_notes,
                ];
            })->values()->toArray() ?? []),
        ];

        return view('citizen.onboarding', [
            'regions' => $regions,
            'prefill' => $prefill,
            'housingTypes' => [
                'owned' => __('messages.housing_types.owned'),
                'rented' => __('messages.housing_types.rented'),
                'family_hosted' => __('messages.housing_types.family_hosted'),
                'other' => __('messages.housing_types.other'),
            ],
            'relations' => [
                self::ALLOWED_MEMBER_RELATION => __('messages.relations.son'),
            ],
        ]);
    }

    /**
     * Store the household from onboarding wizard.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $allowedRegionIds = $this->allowedOnboardingRegions()->pluck('id')->all();

        // Locate any household that may already belong to this user (linked or orphaned)
        $existingHouseholdId = $user->household_id
            ?? Household::where('head_national_id', $user->national_id)->value('id');

        // Normalize boolean inputs (checkboxes send "on")
        $request->merge([
            'has_war_injury' => $request->boolean('has_war_injury'),
            'has_chronic_disease' => $request->boolean('has_chronic_disease'),
            'has_disability' => $request->boolean('has_disability'),
            'payment_account_number' => preg_replace('/\D+/', '', (string) $request->input('payment_account_number', '')),
            'payment_account_holder_name' => trim((string) $request->input('payment_account_holder_name')),
            'condition_type' => trim((string) $request->input('condition_type')),
        ]);

        if (is_array($request->input('members'))) {
            $normalizedMembers = collect($request->input('members'))->map(function ($member) {
                return array_merge($member, [
                    'has_war_injury' => isset($member['has_war_injury']) ? filter_var($member['has_war_injury'], FILTER_VALIDATE_BOOLEAN) : false,
                    'has_chronic_disease' => isset($member['has_chronic_disease']) ? filter_var($member['has_chronic_disease'], FILTER_VALIDATE_BOOLEAN) : false,
                    'has_disability' => isset($member['has_disability']) ? filter_var($member['has_disability'], FILTER_VALIDATE_BOOLEAN) : false,
                    'condition_type' => trim((string) ($member['condition_type'] ?? '')),
                ]);
            })->toArray();
            $request->merge(['members' => $normalizedMembers]);
        }

        $validator = Validator::make($request->all(), [
            // Step 1: Region & Address
            'region_id' => ['required', Rule::in($allowedRegionIds)],
            'address_text' => ['required', 'string', 'max:500'],
            'previous_governorate' => ['required', 'string', 'max:100'],
            'previous_area' => ['required', 'string', 'max:100'],
            'payment_account_type' => ['required', 'in:wallet,bank'],
            'payment_account_number' => ['required', 'digits_between:6,30'],
            'payment_account_holder_name' => ['required', 'string', 'max:255'],
            
            // Step 2: Housing & Contact
            'housing_type' => ['required', 'in:owned,rented,family_hosted,other'],
            'primary_phone' => ['required', 'digits:10'],
            'secondary_phone' => ['nullable', 'digits:10'],
            'has_war_injury' => ['nullable', 'boolean'],
            'has_chronic_disease' => ['nullable', 'boolean'],
            'has_disability' => ['nullable', 'boolean'],
            'condition_type' => ['nullable', 'string', 'max:255'],
            'condition_notes' => ['nullable', 'string', 'max:1000'],
            
            // Step 3: Members (optional array)
            'members' => ['nullable', 'array', 'max:20'],
            'members.*.full_name' => ['required_with:members', 'string', 'max:255'],
            'members.*.national_id' => [
                'required_with:members',
                'digits:9',
                Rule::unique('household_members', 'national_id')->where(function ($query) use ($existingHouseholdId) {
                    if ($existingHouseholdId) {
                        $query->where('household_id', '!=', $existingHouseholdId);
                    }
                }),
            ],
            'members.*.relation_to_head' => ['required_with:members', Rule::in([self::ALLOWED_MEMBER_RELATION])],
            'members.*.gender' => ['nullable', 'in:male,female'],
            'members.*.birth_date' => ['nullable', 'date', 'before:today'],
            'members.*.has_war_injury' => ['nullable', 'boolean'],
            'members.*.has_chronic_disease' => ['nullable', 'boolean'],
            'members.*.has_disability' => ['nullable', 'boolean'],
            'members.*.condition_type' => ['nullable', 'string', 'max:255'],
            'members.*.health_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'region_id.in' => __('messages.onboarding_form.region_not_allowed'),
        ]);

        $validator->after(function ($validator) use ($request) {
            $householdHasHealthFlag = $request->boolean('has_war_injury')
                || $request->boolean('has_chronic_disease')
                || $request->boolean('has_disability');

            if ($householdHasHealthFlag && blank($request->input('condition_type'))) {
                $validator->errors()->add('condition_type', __('validation.required', ['attribute' => __('messages.health.condition_type')]));
            }

            foreach ($request->input('members', []) as $index => $member) {
                $hasFlag = filter_var(Arr::get($member, 'has_war_injury'), FILTER_VALIDATE_BOOLEAN)
                    || filter_var(Arr::get($member, 'has_chronic_disease'), FILTER_VALIDATE_BOOLEAN)
                    || filter_var(Arr::get($member, 'has_disability'), FILTER_VALIDATE_BOOLEAN);

                $conditionType = trim((string) Arr::get($member, 'condition_type'));

                if ($hasFlag && $conditionType === '') {
                    $validator->errors()->add("members.$index.condition_type", __('validation.required', ['attribute' => __('messages.health.condition_type')]));
                }
            }
        });

        $validated = $validator->validate();

        DB::transaction(function () use ($user, $validated, $request, $existingHouseholdId) {
            $householdHasHealthFlag = $request->boolean('has_war_injury')
                || $request->boolean('has_chronic_disease')
                || $request->boolean('has_disability');

            $payload = [
                'head_national_id' => $user->national_id,
                'head_name' => $user->full_name,
                'region_id' => $validated['region_id'],
                'address_text' => $validated['address_text'],
                'previous_governorate' => $validated['previous_governorate'],
                'previous_area' => $validated['previous_area'],
                'payment_account_type' => $validated['payment_account_type'],
                'payment_account_number' => $validated['payment_account_number'],
                'payment_account_holder_name' => $validated['payment_account_holder_name'],
                'housing_type' => $validated['housing_type'],
                'primary_phone' => $validated['primary_phone'],
                'secondary_phone' => $validated['secondary_phone'] ?? null,
                'status' => 'pending', // Needs verification
                'has_war_injury' => $request->boolean('has_war_injury'),
                'has_chronic_disease' => $request->boolean('has_chronic_disease'),
                'has_disability' => $request->boolean('has_disability'),
                'condition_type' => $householdHasHealthFlag ? ($validated['condition_type'] ?? null) : null,
                'condition_notes' => $validated['condition_notes'] ?? null,
            ];

            // Create or update household atomically
            $household = $existingHouseholdId
                ? Household::find($existingHouseholdId)
                : null;

            $before = $household?->toArray();

            if ($household) {
                $household->update($payload);
                $action = 'update';
            } else {
                $household = Household::create($payload);
                $action = 'create';
            }

            // Refresh members to match submission (idempotent)
            $household->members()->delete();

            foreach ($validated['members'] ?? [] as $memberData) {
                if (!empty($memberData['full_name'])) {
                    $memberHasHealthFlag = (bool) ($memberData['has_war_injury'] ?? false)
                        || (bool) ($memberData['has_chronic_disease'] ?? false)
                        || (bool) ($memberData['has_disability'] ?? false);

                    $household->members()->create([
                        'full_name' => $memberData['full_name'],
                        'national_id' => $memberData['national_id'] ?? null,
                        'relation_to_head' => $memberData['relation_to_head'],
                        'gender' => $memberData['gender'] ?? null,
                        'birth_date' => $memberData['birth_date'] ?? null,
                        'has_war_injury' => (bool)($memberData['has_war_injury'] ?? false),
                        'has_chronic_disease' => (bool)($memberData['has_chronic_disease'] ?? false),
                        'has_disability' => (bool)($memberData['has_disability'] ?? false),
                        'condition_type' => $memberHasHealthFlag ? ($memberData['condition_type'] ?? null) : null,
                        'health_notes' => $memberData['health_notes'] ?? null,
                    ]);
                }
            }

            // Link user to household
            $user->household_id = $household->id;
            $user->save();

            // Audit log
            AuditLog::log(
                $action,
                'Household',
                $household->id,
                $before,
                $household->fresh()->toArray()
            );
        });

        return redirect()->route('citizen.dashboard')
            ->with('success', 'Your household has been registered successfully! It is pending verification.');
    }

    private function allowedOnboardingRegions()
    {
        $parentRegion = Region::query()->firstOrCreate(
            ['code' => self::ALLOWED_ONBOARDING_PARENT_CODE],
            [
                'name' => self::ALLOWED_ONBOARDING_PARENT_NAME,
                'is_active' => true,
            ]
        );

        foreach (Region::ALLOWED_CAMP_REGION_NAMES as $name) {
            $code = self::ALLOWED_ONBOARDING_REGION_CODES[$name] ?? null;
            $region = null;

            // Prefer matching by unique code first to avoid duplicate-key inserts.
            if ($code) {
                $region = Region::query()->where('code', $code)->first();
            }

            // Fallback for older records that may have legacy name formatting.
            if (!$region) {
                $region = Region::query()->where('name', $name)->first();
            }

            if (!$region) {
                $region = new Region();
            }

            $region->name = $name;
            if ($code) {
                $region->code = $code;
            }
            $region->parent_id = $parentRegion->id;
            $region->is_active = true;

            if ($region->isDirty()) {
                $region->save();
            }
        }

        $allowedCodes = array_values(self::ALLOWED_ONBOARDING_REGION_CODES);
        $regions = Region::query()
            ->select(['id', 'name', 'code'])
            ->where('is_active', true)
            ->whereIn('code', $allowedCodes)
            ->get();

        $order = array_flip($allowedCodes);

        return $regions
            ->sortBy(static fn (Region $region) => $order[$region->code] ?? PHP_INT_MAX)
            ->values();
    }
}
