<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Household;
use App\Models\HouseholdMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    /**
     * Relations list for dropdown.
     */
    public static array $relations = [
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
    ];

    /**
     * Show members management page.
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if (!$user->household_id) {
            return redirect()->route('citizen.onboarding');
        }

        $household = Household::with('members')->find($user->household_id);

        return view('citizen.members.index', [
            'household' => $household,
            'members' => $household->members,
            'relations' => self::$relations,
        ]);
    }

    /**
     * Store a new member.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        if (!$user->household_id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No household found'], 400);
            }
            return redirect()->route('citizen.onboarding');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:20', 'unique:household_members,national_id'],
            'relation_to_head' => ['required', 'string', 'max:50'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date', 'before:today'],
        ]);

        $validated['household_id'] = $user->household_id;

        $member = HouseholdMember::create($validated);

        AuditLog::log(
            'create',
            'HouseholdMember',
            $member->id,
            null,
            $member->toArray()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'member' => $member,
                'message' => 'Member added successfully!',
            ]);
        }

        return back()->with('success', 'Member added successfully!');
    }

    /**
     * Update a member.
     */
    public function update(Request $request, HouseholdMember $member): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        // Check ownership
        if ($member->household_id !== $user->household_id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:20', 'unique:household_members,national_id,' . $member->id],
            'relation_to_head' => ['required', 'string', 'max:50'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date', 'before:today'],
        ]);

        $before = $member->toArray();
        $member->update($validated);

        AuditLog::log(
            'update',
            'HouseholdMember',
            $member->id,
            $before,
            $member->fresh()->toArray()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'member' => $member->fresh(),
                'message' => 'Member updated successfully!',
            ]);
        }

        return back()->with('success', 'Member updated successfully!');
    }

    /**
     * Delete a member.
     */
    public function destroy(Request $request, HouseholdMember $member): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        // Check ownership
        if ($member->household_id !== $user->household_id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $before = $member->toArray();
        $memberId = $member->id;
        $member->delete();

        AuditLog::log(
            'delete',
            'HouseholdMember',
            $memberId,
            $before,
            null
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Member removed successfully!',
            ]);
        }

        return back()->with('success', 'Member removed successfully!');
    }
}
