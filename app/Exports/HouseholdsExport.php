<?php

namespace App\Exports;

use App\Models\Household;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class HouseholdsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Household::with(['region', 'members']);

        if ($status = $this->request->input('status')) {
            $query->where('status', $status);
        }

        if ($regionId = $this->request->input('region_id')) {
            $query->where('region_id', $regionId);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            __('messages.exports.households.national_id'),
            __('messages.exports.households.head_name'),
            __('messages.exports.households.region'),
            __('messages.exports.households.address'),
            __('messages.exports.households.housing_type'),
            __('messages.exports.households.primary_phone'),
            __('messages.exports.households.secondary_phone'),
            __('messages.exports.households.status'),
            __('messages.exports.households.members_count'),
            __('messages.exports.households.member_names'),
            __('messages.exports.households.registered_date'),
        ];
    }

    public function map($household): array
    {
        return [
            $household->head_national_id,
            $household->head_name,
            $household->region->name ?? '',
            $household->address_text,
            $household->housing_type ? __('messages.housing_types.' . $household->housing_type) : '',
            $household->primary_phone,
            $household->secondary_phone,
            __('messages.status.' . $household->status),
            $household->members->count(),
            $household->members->pluck('full_name')->implode('، '),
            $household->created_at->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0D9488'],
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }
}
