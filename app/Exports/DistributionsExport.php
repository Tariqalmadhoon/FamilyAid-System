<?php

namespace App\Exports;

use App\Models\Distribution;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Http\Request;

class DistributionsExport
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Generate the Excel file and return the file path
     */
    public function generate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set RTL for Arabic content
        $sheet->setRightToLeft(true);
        
        // Set sheet title
        $sheet->setTitle('التوزيعات');
        
        // Define headers
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
        
        // Write headers
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($headers as $index => $header) {
            $cell = $columns[$index] . '1';
            $sheet->setCellValue($cell, $header);
        }
        
        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D9488'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        
        // Get data
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
        
        $distributions = $query->orderBy('distribution_date', 'desc')->get();
        
        // Write data
        $row = 2;
        foreach ($distributions as $distribution) {
            $sheet->setCellValue('A' . $row, $distribution->distribution_date ? $distribution->distribution_date->format('Y-m-d') : '');
            $sheet->setCellValue('B' . $row, $distribution->aidProgram->name ?? '');
            $sheet->setCellValue('C' . $row, $distribution->household->head_national_id ?? '');
            $sheet->setCellValue('D' . $row, $distribution->household->head_name ?? '');
            $sheet->setCellValue('E' . $row, $distribution->household->region->name ?? '');
            $sheet->setCellValue('F' . $row, $distribution->household->primary_phone ?? '');
            $sheet->setCellValue('G' . $row, $distribution->distributor->name ?? __('messages.general.system'));
            $sheet->setCellValue('H' . $row, $distribution->notes ?? '');
            $row++;
        }
        
        // Auto-size columns
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Generate unique filename
        $filename = 'distributions_' . date('Y-m-d_His') . '.xlsx';
        $filePath = storage_path('app/exports/' . $filename);
        
        // Ensure directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        // Write file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        
        // Clean up
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        
        return $filePath;
    }
}
