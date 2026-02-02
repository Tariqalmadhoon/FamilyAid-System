<?php
/**
 * Direct Excel Export - Working version
 * Access via: /direct-export.php?type=households
 *             /direct-export.php?type=distributions
 */

// Prevent any output
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Load autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Bootstrap Laravel for database access
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Get export type
$type = $_GET['type'] ?? 'households';

try {
    if ($type === 'households') {
        $filename = 'households_' . date('Y-m-d_His') . '.xlsx';
        $spreadsheet = createHouseholdsExport();
    } elseif ($type === 'distributions') {
        $filename = 'distributions_' . date('Y-m-d_His') . '.xlsx';
        $spreadsheet = createDistributionsExport();
    } else {
        throw new Exception('Invalid export type');
    }
    
    // Save to temp file
    $tempFile = sys_get_temp_dir() . '/' . $filename;
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);
    
    // Clean up spreadsheet
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    
    // Get file size
    $fileSize = filesize($tempFile);
    
    // Clear any buffered output
    ob_end_clean();
    
    // Send headers
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . $fileSize);
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Expires: 0');
    
    // Output file
    readfile($tempFile);
    
    // Delete temp file
    unlink($tempFile);
    
    exit;
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Export Error: ' . $e->getMessage();
    exit;
}

/**
 * Create Households Export Spreadsheet
 */
function createHouseholdsExport(): Spreadsheet
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set RTL
    $sheet->setRightToLeft(true);
    $sheet->setTitle('الأسر');
    
    // Headers - Added # as first column
    $headers = [
        'A' => '#',
        'B' => __('messages.exports.households.national_id'),
        'C' => __('messages.exports.households.head_name'),
        'D' => __('messages.exports.households.region'),
        'E' => __('messages.exports.households.address'),
        'F' => __('messages.exports.households.housing_type'),
        'G' => __('messages.exports.households.primary_phone'),
        'H' => __('messages.exports.households.secondary_phone'),
        'I' => __('messages.exports.households.status'),
        'J' => __('messages.exports.households.members_count'),
        'K' => __('messages.exports.households.member_names'),
        'L' => __('messages.exports.households.registered_date'),
    ];
    
    foreach ($headers as $col => $header) {
        $sheet->setCellValue($col . '1', $header);
    }
    
    // Style headers
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D9488']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ];
    $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
    
    // Get data
    $query = \App\Models\Household::with(['region', 'members']);
    
    if (!empty($_GET['status'])) {
        $query->where('status', $_GET['status']);
    }
    
    if (!empty($_GET['region_id'])) {
        $query->where('region_id', $_GET['region_id']);
    }
    
    $households = $query->orderBy('created_at', 'desc')->get();
    
    // Write data
    $row = 2;
    $sequenceNumber = 1;
    foreach ($households as $household) {
        $sheet->setCellValue('A' . $row, $sequenceNumber);
        $sheet->setCellValue('B' . $row, $household->head_national_id ?? '');
        $sheet->setCellValue('C' . $row, $household->head_name ?? '');
        $sheet->setCellValue('D' . $row, $household->region->name ?? '');
        $sheet->setCellValue('E' . $row, $household->address_text ?? '');
        $sheet->setCellValue('F' . $row, $household->housing_type ? __('messages.housing_types.' . $household->housing_type) : '');
        $sheet->setCellValue('G' . $row, $household->primary_phone ?? '');
        $sheet->setCellValue('H' . $row, $household->secondary_phone ?? '');
        $sheet->setCellValue('I' . $row, __('messages.status.' . $household->status));
        $sheet->setCellValue('J' . $row, $household->members->count());
        $sheet->setCellValue('K' . $row, $household->members->pluck('full_name')->implode('، '));
        $sheet->setCellValue('L' . $row, $household->created_at ? $household->created_at->format('Y-m-d') : '');
        $row++;
        $sequenceNumber++;
    }
    
    // Auto-size columns
    foreach (array_keys($headers) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    return $spreadsheet;
}

/**
 * Create Distributions Export Spreadsheet
 */
function createDistributionsExport(): Spreadsheet
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set RTL
    $sheet->setRightToLeft(true);
    $sheet->setTitle('التوزيعات');
    
    // Headers - Added # as first column
    $headers = [
        'A' => '#',
        'B' => __('messages.exports.distributions.date'),
        'C' => __('messages.exports.distributions.program'),
        'D' => __('messages.exports.distributions.national_id'),
        'E' => __('messages.exports.distributions.head_name'),
        'F' => __('messages.exports.distributions.region'),
        'G' => __('messages.exports.distributions.phone'),
        'H' => __('messages.exports.distributions.recorded_by'),
        'I' => __('messages.exports.distributions.notes'),
    ];
    
    foreach ($headers as $col => $header) {
        $sheet->setCellValue($col . '1', $header);
    }
    
    // Style headers
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D9488']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ];
    $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
    
    // Get data
    $query = \App\Models\Distribution::with(['household', 'aidProgram', 'distributor']);
    
    if (!empty($_GET['program_id'])) {
        $query->where('aid_program_id', $_GET['program_id']);
    }
    
    if (!empty($_GET['from_date'])) {
        $query->whereDate('distribution_date', '>=', $_GET['from_date']);
    }
    
    if (!empty($_GET['to_date'])) {
        $query->whereDate('distribution_date', '<=', $_GET['to_date']);
    }
    
    $distributions = $query->orderBy('distribution_date', 'desc')->get();
    
    // Write data
    $row = 2;
    $sequenceNumber = 1;
    foreach ($distributions as $distribution) {
        $sheet->setCellValue('A' . $row, $sequenceNumber);
        $sheet->setCellValue('B' . $row, $distribution->distribution_date ? $distribution->distribution_date->format('Y-m-d') : '');
        $sheet->setCellValue('C' . $row, $distribution->aidProgram->name ?? '');
        $sheet->setCellValue('D' . $row, $distribution->household->head_national_id ?? '');
        $sheet->setCellValue('E' . $row, $distribution->household->head_name ?? '');
        $sheet->setCellValue('F' . $row, $distribution->household->region->name ?? '');
        $sheet->setCellValue('G' . $row, $distribution->household->primary_phone ?? '');
        $sheet->setCellValue('H' . $row, $distribution->distributor->name ?? __('messages.general.system'));
        $sheet->setCellValue('I' . $row, $distribution->notes ?? '');
        $row++;
        $sequenceNumber++;
    }
    
    // Auto-size columns
    foreach (array_keys($headers) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    return $spreadsheet;
}
