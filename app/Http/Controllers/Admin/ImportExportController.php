<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DistributionsExport;
use App\Exports\HouseholdsExport;
use App\Http\Controllers\Admin\Concerns\InteractsWithCampAccess;
use App\Http\Controllers\Controller;
use App\Imports\HouseholdsImport;
use App\Models\AuditLog;
use App\Models\ImportJob;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends Controller
{
    use InteractsWithCampAccess;

    /**
     * Show import/export page.
     */
    public function index(): View
    {
        $this->ensureAny(['households.import', 'households.export', 'distributions.export']);

        $recentImports = ImportJob::with('user')
            ->visibleTo($this->currentUser())
            ->latest()
            ->limit(10)
            ->get();

        $regions = $this->visibleCampRegionTree();

        return view('admin.import-export.index', [
            'recentImports' => $recentImports,
            'regions' => $regions,
            'isCampManager' => $this->isCampManager(),
            'managedRegionId' => $this->managedRegionId(),
        ]);
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        $this->ensureCan('households.import');

        $headers = [
            'national_id',
            'head_name',
            'head_birth_date',
            'spouse_full_name',
            'spouse_national_id',
            'spouse_birth_date',
            'spouse_war_injury',
            'spouse_chronic_disease',
            'spouse_disability',
            'spouse_condition_type',
            'spouse_health_notes',
            'region',
            'address',
            'housing_type',
            'phone',
            'member_names',
            'member_relations',
        ];

        $example = [
            '123456789',
            'John Doe',
            '1987-03-10',
            'Jane Doe',
            '987654321',
            '1990-05-12',
            '0',
            '1',
            '0',
            'Diabetes',
            'Needs periodic medication',
            'District A',
            '123 Main Street',
            'owned',
            '0501234567',
            'Jane Doe, Bob Doe',
            'spouse, son',
        ];

        $callback = function() use ($headers, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->streamDownload($callback, 'households_import_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Import households from Excel/CSV.
     */
    public function import(Request $request): RedirectResponse
    {
        $this->ensureCan('households.import');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $importJob = ImportJob::create([
            'user_id'   => auth()->id(),
            'file_name' => $request->file('file')->getClientOriginalName(),
            'type'      => 'households',
            'status'    => 'processing',
        ]);

        try {
            $import = new HouseholdsImport($this->currentUser());
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $failureCount = $import->getFailureCount();

            $importJob->update([
                'status'        => 'completed',
                'total_rows'    => $successCount + $failureCount,
                'success_count' => $successCount,
                'error_count'   => $failureCount,
                'errors_json'   => $import->getErrors(),
            ]);

            AuditLog::log('import', 'ImportJob', $importJob->id, null, [
                'file' => $importJob->file_name,
                'success' => $import->getSuccessCount(),
                'failed' => $import->getFailureCount(),
            ]);

            if ($import->getFailureCount() > 0) {
                return back()->with('warning', "Import completed with {$import->getSuccessCount()} successful and {$import->getFailureCount()} failed rows.");
            }

            return back()->with('success', "Successfully imported {$import->getSuccessCount()} households!");

        } catch (\Exception $e) {
            $importJob->update([
                'status'      => 'failed',
                'errors_json' => [$e->getMessage()],
            ]);

            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Export households to Excel.
     */
    public function exportHouseholds(Request $request)
    {
        $this->ensureCan('households.export');

        AuditLog::log('export', 'Household', null, null, [
            'filters' => [
                ...$request->only(['status', 'region_id']),
                'resolved_region_id' => $this->enforcedRegionId($request->input('region_id')),
            ],
        ]);

        try {
            $export = new HouseholdsExport($request, $this->currentUser());
            $filePath = $export->generate();
            $filename = basename($filePath);

            $response = response()->download(
                $filePath,
                $filename,
                [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                    'X-Content-Type-Options' => 'nosniff',
                ]
            );

            $response->setContentDisposition('attachment', $filename);

            return $response->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export distributions to Excel.
     */
    public function exportDistributions(Request $request)
    {
        $this->ensureCan('distributions.export');

        AuditLog::log('export', 'Distribution', null, null, [
            'filters' => [
                ...$request->only(['program_id', 'from_date', 'to_date', 'region_id']),
                'resolved_region_id' => $this->enforcedRegionId($request->input('region_id')),
            ],
        ]);

        try {
            $export = new DistributionsExport($request, $this->currentUser());
            $filePath = $export->generate();
            $filename = basename($filePath);

            $response = response()->download(
                $filePath,
                $filename,
                [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                    'X-Content-Type-Options' => 'nosniff',
                ]
            );

            $response->setContentDisposition('attachment', $filename);

            return $response->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
}

