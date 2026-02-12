<?php

namespace App\Exports;

use App\Models\Household;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Http\Request;

class HouseholdsExport
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
        $sheet->setTitle('الأسر');
        
        // Define headers
        $headers = [
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
            __('messages.exports.households.previous_governorate'),
            __('messages.exports.households.previous_area'),
            __('messages.exports.households.registered_date'),
        ];
        
        // Write headers
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
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
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        
        // Get data
        $query = Household::with(['region', 'members']);
        
        if ($status = $this->request->input('status')) {
            $query->where('status', $status);
        }
        
        if ($regionId = $this->request->input('region_id')) {
            $query->where('region_id', $regionId);
        }
        
        $households = $query->orderBy('created_at', 'desc')->get();

        // Load governorate/area translation maps for display
        $governorateLabels = __('messages.previous_governorates');
        $areaLabels = __('messages.previous_areas');
        
        // Write data
        $row = 2;
        foreach ($households as $household) {
            $sheet->setCellValue('A' . $row, $household->head_national_id ?? '');
            $sheet->setCellValue('B' . $row, $household->head_name ?? '');
            $sheet->setCellValue('C' . $row, $household->region->name ?? '');
            $sheet->setCellValue('D' . $row, $household->address_text ?? '');
            $sheet->setCellValue('E' . $row, $household->housing_type ? __('messages.housing_types.' . $household->housing_type) : '');
            $sheet->setCellValue('F' . $row, $household->primary_phone ?? '');
            $sheet->setCellValue('G' . $row, $household->secondary_phone ?? '');
            $sheet->setCellValue('H' . $row, __('messages.status.' . $household->status));
            $sheet->setCellValue('I' . $row, $household->members->count());
            $sheet->setCellValue('J' . $row, $household->members->pluck('full_name')->implode('، '));
            $sheet->setCellValue('K' . $row, $governorateLabels[$household->previous_governorate] ?? ($household->previous_governorate ?? ''));
            $sheet->setCellValue('L' . $row, $areaLabels[$household->previous_governorate][$household->previous_area] ?? ($household->previous_area ?? ''));
            $sheet->setCellValue('M' . $row, $household->created_at ? $household->created_at->format('Y-m-d') : '');
            $row++;
        }
        
        // Auto-size columns
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Generate unique filename
        $filename = 'households_' . date('Y-m-d_His') . '.xlsx';
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
