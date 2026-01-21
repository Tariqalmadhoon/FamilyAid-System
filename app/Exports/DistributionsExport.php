<?php

namespace App\Exports;

use App\Models\Distribution;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class DistributionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Distribution::with(['household', 'aidProgram', 'distributor']);

        if ($programId = $this->request->input('program_id')) {
            $query->where('aid_program_id', $programId);
        }

        if ($from = $this->request->input('from_date')) {
            $query->whereDate('distribution_date', '>=', $from);
        }

        if ($to = $this->request->input('to_date')) {
            $query->whereDate('distribution_date', '<=', $to);
        }

        return $query->orderBy('distribution_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'Date',
            'Program',
            'National ID',
            'Head Name',
            'Region',
            'Phone',
            'Recorded By',
            'Notes',
        ];
    }

    public function map($distribution): array
    {
        return [
            $distribution->distribution_date->format('Y-m-d'),
            $distribution->aidProgram->name ?? '',
            $distribution->household->head_national_id ?? '',
            $distribution->household->head_name ?? '',
            $distribution->household->region->name ?? '',
            $distribution->household->primary_phone ?? '',
            $distribution->distributor->name ?? 'System',
            $distribution->notes,
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
