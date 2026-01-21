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
            'National ID',
            'Head Name',
            'Region',
            'Address',
            'Housing Type',
            'Primary Phone',
            'Secondary Phone',
            'Status',
            'Members Count',
            'Member Names',
            'Registered Date',
        ];
    }

    public function map($household): array
    {
        return [
            $household->head_national_id,
            $household->head_name,
            $household->region->name ?? '',
            $household->address_text,
            ucfirst(str_replace('_', ' ', $household->housing_type ?? '')),
            $household->primary_phone,
            $household->secondary_phone,
            ucfirst($household->status),
            $household->members->count(),
            $household->members->pluck('full_name')->implode(', '),
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
