<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DistributionsExport;
use App\Exports\HouseholdsExport;
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
    /**
     * Show import/export page.
     */
    public function index(): View
    {
        $recentImports = ImportJob::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $regions = Region::query()
            ->with(['children' => function ($query) {
                $query->allowedCamps();
            }])
            ->whereNull('parent_id')
            ->whereHas('children', function ($query) {
                $query->allowedCamps();
            })
            ->get();

        return view('admin.import-export.index', [
            'recentImports' => $recentImports,
            'regions' => $regions,
        ]);
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'national_id',
            'head_name',
            'region',
            'address',
            'housing_type',
            'phone',
            'member_names',
            'member_relations',
        ];

        $example = [
            '1234567890',
            'John Doe',
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
            $import = new HouseholdsImport();
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
        AuditLog::log('export', 'Household', null, null, [
            'filters' => $request->only(['status', 'region_id']),
        ]);

        try {
            $export = new HouseholdsExport($request);
            $filePath = $export->generate();
            
            $filename = basename($filePath);
            
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export distributions to Excel.
     */
    public function exportDistributions(Request $request)
    {
        AuditLog::log('export', 'Distribution', null, null, [
            'filters' => $request->only(['program_id', 'from_date', 'to_date']),
        ]);

        try {
            $export = new DistributionsExport($request);
            $filePath = $export->generate();
            
            $filename = basename($filePath);
            
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
}
