<?php
/**
 * Direct Excel Export - Working version
 * Access via: /direct-export.php?type=households
 *             /direct-export.php?type=distributions
 */

if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

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

    $tempFile = sys_get_temp_dir() . '/' . $filename;
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);

    $fileSize = filesize($tempFile);

    ob_end_clean();

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . $fileSize);
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Expires: 0');

    readfile($tempFile);
    unlink($tempFile);
    exit;
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Export Error: ' . $e->getMessage();
    exit;
}

function createHouseholdsExport(): Spreadsheet
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setRightToLeft(true);
    $sheet->setTitle((string) __('messages.nav.households'));

    $headers = [
        'A' => '#',
        'B' => __('messages.exports.households.national_id'),
        'C' => __('messages.exports.households.head_name'),
        'D' => __('messages.exports.households.head_birth_date'),
        'E' => __('messages.exports.households.spouse_full_name'),
        'F' => __('messages.exports.households.spouse_national_id'),
        'G' => __('messages.exports.households.spouse_birth_date'),
        'H' => __('messages.exports.households.spouse_has_war_injury'),
        'I' => __('messages.exports.households.spouse_has_chronic_disease'),
        'J' => __('messages.exports.households.spouse_has_disability'),
        'K' => __('messages.exports.households.spouse_condition_type'),
        'L' => __('messages.exports.households.spouse_health_notes'),
        'M' => __('messages.exports.households.region'),
        'N' => __('messages.exports.households.address'),
        'O' => __('messages.exports.households.housing_type'),
        'P' => __('messages.exports.households.primary_phone'),
        'Q' => __('messages.exports.households.secondary_phone'),
        'R' => __('messages.exports.households.payment_account_type'),
        'S' => __('messages.exports.households.payment_account_number'),
        'T' => __('messages.exports.households.payment_account_holder_name'),
        'U' => __('messages.exports.households.status'),
        'V' => __('messages.exports.households.members_count'),
        'W' => __('messages.exports.households.member_names'),
        'X' => __('messages.exports.households.previous_governorate'),
        'Y' => __('messages.exports.households.previous_area'),
        'Z' => __('messages.exports.households.registered_date'),
    ];

    foreach ($headers as $col => $header) {
        $sheet->setCellValue($col . '1', $header);
    }

    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D9488']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ];
    $sheet->getStyle('A1:Z1')->applyFromArray($headerStyle);

    $query = \App\Models\Household::with(['region', 'members']);

    if (!empty($_GET['status'])) {
        $query->where('status', $_GET['status']);
    }

    if (!empty($_GET['region_id'])) {
        $query->where('region_id', $_GET['region_id']);
    }

    $households = $query->orderBy('created_at', 'desc')->get();
    $governorateLabels = __('messages.previous_governorates');
    $areaLabels = __('messages.previous_areas');

    $row = 2;
    $sequenceNumber = 1;
    foreach ($households as $household) {
        $sheet->setCellValue('A' . $row, $sequenceNumber);
        $sheet->setCellValue('B' . $row, $household->head_national_id ?? '');
        $sheet->setCellValue('C' . $row, $household->head_name ?? '');
        $sheet->setCellValue('D' . $row, $household->head_birth_date ? $household->head_birth_date->format('Y-m-d') : '');
        $sheet->setCellValue('E' . $row, $household->spouse_full_name ?? '');
        $sheet->setCellValue('F' . $row, $household->spouse_national_id ?? '');
        $sheet->setCellValue('G' . $row, $household->spouse_birth_date ? $household->spouse_birth_date->format('Y-m-d') : '');
        $sheet->setCellValue('H' . $row, $household->spouse_has_war_injury ? 1 : 0);
        $sheet->setCellValue('I' . $row, $household->spouse_has_chronic_disease ? 1 : 0);
        $sheet->setCellValue('J' . $row, $household->spouse_has_disability ? 1 : 0);
        $sheet->setCellValue('K' . $row, $household->spouse_condition_type ?? '');
        $sheet->setCellValue('L' . $row, $household->spouse_health_notes ?? '');
        $sheet->setCellValue('M' . $row, $household->region->name ?? '');
        $sheet->setCellValue('N' . $row, $household->address_text ?? '');
        $sheet->setCellValue('O' . $row, $household->housing_type ? __('messages.housing_types.' . $household->housing_type) : '');
        $sheet->setCellValue('P' . $row, $household->primary_phone ?? '');
        $sheet->setCellValue('Q' . $row, $household->secondary_phone ?? '');
        $sheet->setCellValue('R' . $row, $household->payment_account_type ? __('messages.account_types.' . $household->payment_account_type) : '');
        $sheet->setCellValue('S' . $row, $household->payment_account_number ?? '');
        $sheet->setCellValue('T' . $row, $household->payment_account_holder_name ?? '');
        $sheet->setCellValue('U' . $row, __('messages.status.' . $household->status));
        $sheet->setCellValue('V' . $row, $household->members->count());
        $sheet->setCellValue('W' . $row, $household->members->pluck('full_name')->implode(', '));
        $sheet->setCellValue('X' . $row, $governorateLabels[$household->previous_governorate] ?? ($household->previous_governorate ?? ''));
        $sheet->setCellValue('Y' . $row, $areaLabels[$household->previous_governorate][$household->previous_area] ?? ($household->previous_area ?? ''));
        $sheet->setCellValue('Z' . $row, $household->created_at ? $household->created_at->format('Y-m-d') : '');
        $row++;
        $sequenceNumber++;
    }

    foreach (array_keys($headers) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    return $spreadsheet;
}

function createDistributionsExport(): Spreadsheet
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setRightToLeft(true);
    $sheet->setTitle((string) __('messages.distributions.title'));

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

    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D9488']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ];
    $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

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

    foreach (array_keys($headers) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    return $spreadsheet;
}
