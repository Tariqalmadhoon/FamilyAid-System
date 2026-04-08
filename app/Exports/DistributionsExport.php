<?php

namespace App\Exports;

use App\Models\Distribution;
use App\Models\User;
use Illuminate\Http\Request;

class DistributionsExport
{
    protected Request $request;
    protected ?User $user;

    public function __construct(Request $request, ?User $user = null)
    {
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * Generate a UTF-8 BOM CSV file that opens cleanly in Excel/WPS.
     */
    public function generate(): string
    {
        $headers = [
            __('messages.exports.distributions.date'),
            __('messages.exports.distributions.program'),
            __('messages.exports.distributions.national_id'),
            __('messages.exports.distributions.head_name'),
            __('messages.exports.distributions.region'),
            __('messages.exports.distributions.phone'),
            __('messages.exports.distributions.recorded_by'),
            __('messages.exports.distributions.notes'),
        ];

        $query = Distribution::with(['household.region', 'aidProgram', 'distributor'])
            ->visibleTo($this->user);

        if ($programId = $this->request->input('program_id')) {
            $query->where('aid_program_id', $programId);
        }

        if ($from = $this->request->input('from_date')) {
            $query->whereDate('distribution_date', '>=', $from);
        }

        if ($to = $this->request->input('to_date')) {
            $query->whereDate('distribution_date', '<=', $to);
        }

        $regionId = $this->user?->isCampManager()
            ? $this->user->managedRegionId()
            : $this->request->input('region_id');

        if ($regionId) {
            $query->whereHas('household', function ($householdQuery) use ($regionId) {
                $householdQuery->where('region_id', $regionId);
            });
        }

        $distributions = $query->orderBy('distribution_date', 'desc')->get();

        $filename = 'distributions_' . date('Y-m-d_His') . '.csv';
        $filePath = storage_path('app/exports/' . $filename);

        if (! is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $handle = fopen($filePath, 'wb');

        if ($handle === false) {
            throw new \RuntimeException('Unable to create distributions export file.');
        }

        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, $headers);

        foreach ($distributions as $distribution) {
            fputcsv($handle, [
                $distribution->distribution_date ? $distribution->distribution_date->format('Y-m-d') : '',
                $distribution->aidProgram->name ?? '',
                $distribution->household->head_national_id ?? '',
                $distribution->household->head_name ?? '',
                $distribution->household->region->name ?? '',
                $distribution->household->primary_phone ?? '',
                $distribution->distributor->name ?? __('messages.general.system'),
                $distribution->notes ?? '',
            ]);
        }

        fclose($handle);

        return $filePath;
    }
}
