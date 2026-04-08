<?php

namespace App\Exports;

use App\Models\Household;
use App\Models\User;
use Illuminate\Http\Request;

class HouseholdsExport
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
            __('messages.exports.households.national_id'),
            __('messages.exports.households.head_name'),
            __('messages.exports.households.head_birth_date'),
            __('messages.exports.households.spouse_full_name'),
            __('messages.exports.households.spouse_national_id'),
            __('messages.exports.households.spouse_birth_date'),
            __('messages.exports.households.spouse_has_war_injury'),
            __('messages.exports.households.spouse_has_chronic_disease'),
            __('messages.exports.households.spouse_has_disability'),
            __('messages.exports.households.spouse_condition_type'),
            __('messages.exports.households.spouse_health_notes'),
            __('messages.exports.households.region'),
            __('messages.exports.households.address'),
            __('messages.exports.households.housing_type'),
            __('messages.exports.households.primary_phone'),
            __('messages.exports.households.secondary_phone'),
            __('messages.exports.households.payment_account_type'),
            __('messages.exports.households.payment_account_number'),
            __('messages.exports.households.payment_account_holder_name'),
            __('messages.exports.households.status'),
            __('messages.exports.households.members_count'),
            __('messages.exports.households.member_names'),
            __('messages.exports.households.previous_governorate'),
            __('messages.exports.households.previous_area'),
            __('messages.exports.households.registered_date'),
        ];

        $query = Household::with(['region', 'members'])
            ->visibleTo($this->user);

        if ($status = $this->request->input('status')) {
            $query->where('status', $status);
        }

        $regionId = $this->user?->isCampManager()
            ? $this->user->managedRegionId()
            : $this->request->input('region_id');

        if ($regionId) {
            $query->where('region_id', $regionId);
        }

        $households = $query->orderBy('created_at', 'desc')->get();
        $governorateLabels = __('messages.previous_governorates');
        $areaLabels = __('messages.previous_areas');

        $filename = 'households_' . date('Y-m-d_His') . '.csv';
        $filePath = storage_path('app/exports/' . $filename);

        if (! is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $handle = fopen($filePath, 'wb');

        if ($handle === false) {
            throw new \RuntimeException('Unable to create households export file.');
        }

        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, $headers);

        foreach ($households as $household) {
            fputcsv($handle, [
                $household->head_national_id ?? '',
                $household->head_name ?? '',
                $household->head_birth_date ? $household->head_birth_date->format('Y-m-d') : '',
                $household->spouse_full_name ?? '',
                $household->spouse_national_id ?? '',
                $household->spouse_birth_date ? $household->spouse_birth_date->format('Y-m-d') : '',
                $household->spouse_has_war_injury ? 1 : 0,
                $household->spouse_has_chronic_disease ? 1 : 0,
                $household->spouse_has_disability ? 1 : 0,
                $household->spouse_condition_type ?? '',
                $household->spouse_health_notes ?? '',
                $household->region->name ?? '',
                $household->address_text ?? '',
                $household->housing_type ? __('messages.housing_types.' . $household->housing_type) : '',
                $household->primary_phone ?? '',
                $household->secondary_phone ?? '',
                $household->payment_account_type ? __('messages.account_types.' . $household->payment_account_type) : '',
                $household->payment_account_number ?? '',
                $household->payment_account_holder_name ?? '',
                __('messages.status.' . $household->status),
                $household->members->count(),
                $household->members->pluck('full_name')->implode(', '),
                $governorateLabels[$household->previous_governorate] ?? ($household->previous_governorate ?? ''),
                $areaLabels[$household->previous_governorate][$household->previous_area] ?? ($household->previous_area ?? ''),
                $household->created_at ? $household->created_at->format('Y-m-d') : '',
            ]);
        }

        fclose($handle);

        return $filePath;
    }
}
