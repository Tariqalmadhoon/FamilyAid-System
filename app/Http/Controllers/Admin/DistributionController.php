<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AidProgram;
use App\Models\AuditLog;
use App\Models\Distribution;
use App\Models\Household;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistributionController extends Controller
{
    /**
     * Display a listing of distributions.
     */
    public function index(Request $request): View
    {
        $query = Distribution::with(['household', 'aidProgram', 'distributor']);

        // Filter by program
        if ($programId = $request->input('program_id')) {
            $query->where('aid_program_id', $programId);
        }

        // Filter by date range
        if ($from = $request->input('from_date')) {
            $query->whereDate('distribution_date', '>=', $from);
        }
        if ($to = $request->input('to_date')) {
            $query->whereDate('distribution_date', '<=', $to);
        }

        // Search by household
        if ($search = $request->input('search')) {
            $query->whereHas('household', function ($q) use ($search) {
                $q->where('head_name', 'like', "%{$search}%")
                  ->orWhere('head_national_id', 'like', "%{$search}%");
            });
        }

        $distributions = $query->latest('distribution_date')->paginate(20)->withQueryString();

        $programs = AidProgram::active()->get();

        return view('admin.distributions.index', [
            'distributions' => $distributions,
            'programs' => $programs,
            'filters' => $request->only(['program_id', 'from_date', 'to_date', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new distribution.
     */
    public function create(Request $request): View
    {
        $programs = AidProgram::active()->get();
        $household = null;

        // Pre-fill household if provided
        if ($householdId = $request->input('household_id')) {
            $household = Household::find($householdId);
        }

        return view('admin.distributions.create', [
            'programs' => $programs,
            'household' => $household,
        ]);
    }

    /**
     * Search for a household by national ID (AJAX).
     */
    public function searchHousehold(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $households = Household::where('head_national_id', 'like', "%{$query}%")
            ->orWhere('head_name', 'like', "%{$query}%")
            ->orWhere('primary_phone', 'like', "%{$query}%")
            ->with('region')
            ->limit(10)
            ->get()
            ->map(function ($h) {
                return [
                    'id' => $h->id,
                    'head_name' => $h->head_name,
                    'head_national_id' => $h->head_national_id,
                    'region' => $h->region->name ?? 'Unknown',
                    'status' => $h->status,
                ];
            });

        return response()->json($households);
    }

    /**
     * Check if household already received from a program.
     */
    public function checkEligibility(Request $request): JsonResponse
    {
        $householdId = $request->input('household_id');
        $programId = $request->input('program_id');

        $program = AidProgram::find($programId);
        
        if (!$program) {
            return response()->json(['eligible' => false, 'message' => 'Program not found']);
        }

        $existingDistribution = Distribution::where('household_id', $householdId)
            ->where('aid_program_id', $programId)
            ->first();

        if ($existingDistribution && !$program->allow_multiple) {
            return response()->json([
                'eligible' => false,
                'message' => 'This household already received from this program on ' . $existingDistribution->distribution_date->format('M j, Y'),
            ]);
        }

        return response()->json([
            'eligible' => true,
            'message' => 'Household is eligible for this program.',
        ]);
    }

    /**
     * Store a newly created distribution.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'household_id' => ['required', 'exists:households,id'],
            'aid_program_id' => ['required', 'exists:aid_programs,id'],
            'distribution_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Check for duplicates
        $program = AidProgram::find($validated['aid_program_id']);
        $existing = Distribution::where('household_id', $validated['household_id'])
            ->where('aid_program_id', $validated['aid_program_id'])
            ->first();

        if ($existing && !$program->allow_multiple) {
            return back()->withInput()->with('error', 'This household already received from this program!');
        }

        $validated['distributed_by'] = auth()->id();

        $distribution = Distribution::create($validated);

        AuditLog::log('create', 'Distribution', $distribution->id, null, $distribution->toArray());

        return redirect()->route('admin.distributions.index')
            ->with('success', 'Distribution recorded successfully!');
    }

    /**
     * Display the specified distribution.
     */
    public function show(Distribution $distribution): View
    {
        $distribution->load(['household.members', 'aidProgram', 'distributor']);

        return view('admin.distributions.show', [
            'distribution' => $distribution,
        ]);
    }

    /**
     * Remove the specified distribution.
     */
    public function destroy(Distribution $distribution): RedirectResponse
    {
        $before = $distribution->toArray();
        $distributionId = $distribution->id;
        $distribution->delete();

        AuditLog::log('delete', 'Distribution', $distributionId, $before, null);

        return redirect()->route('admin.distributions.index')
            ->with('success', 'Distribution deleted successfully!');
    }
}
