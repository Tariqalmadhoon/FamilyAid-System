<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AidProgram;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AidProgramController extends Controller
{
    /**
     * Display a listing of aid programs.
     */
    public function index(Request $request): View
    {
        $query = AidProgram::withCount('distributions');

        if ($request->input('active_only')) {
            $query->active();
        }

        $programs = $query->latest()->paginate(15);

        return view('admin.programs.index', [
            'programs' => $programs,
        ]);
    }

    /**
     * Show the form for creating a new program.
     */
    public function create(): View
    {
        return view('admin.programs.create');
    }

    /**
     * Store a newly created program.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
            'allow_multiple' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['allow_multiple'] = $request->boolean('allow_multiple');

        $program = AidProgram::create($validated);

        AuditLog::log('create', 'AidProgram', $program->id, null, $program->toArray());

        return redirect()->route('admin.programs.index')
            ->with('success', 'Aid program created successfully!');
    }

    /**
     * Display the specified program.
     */
    public function show(AidProgram $program): View
    {
        $program->loadCount('distributions');
        
        $distributions = $program->distributions()
            ->with(['household', 'distributor'])
            ->latest('distribution_date')
            ->paginate(15);

        return view('admin.programs.show', [
            'program' => $program,
            'distributions' => $distributions,
        ]);
    }

    /**
     * Show the form for editing the specified program.
     */
    public function edit(AidProgram $program): View
    {
        return view('admin.programs.edit', [
            'program' => $program,
        ]);
    }

    /**
     * Update the specified program.
     */
    public function update(Request $request, AidProgram $program): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
            'allow_multiple' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['allow_multiple'] = $request->boolean('allow_multiple');

        $before = $program->toArray();
        $program->update($validated);

        AuditLog::log('update', 'AidProgram', $program->id, $before, $program->fresh()->toArray());

        return redirect()->route('admin.programs.index')
            ->with('success', 'Aid program updated successfully!');
    }

    /**
     * Remove the specified program.
     */
    public function destroy(AidProgram $program): RedirectResponse
    {
        if ($program->distributions()->exists()) {
            return back()->with('error', 'Cannot delete program with existing distributions.');
        }

        $before = $program->toArray();
        $programId = $program->id;
        $program->delete();

        AuditLog::log('delete', 'AidProgram', $programId, $before, null);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Aid program deleted successfully!');
    }
}
