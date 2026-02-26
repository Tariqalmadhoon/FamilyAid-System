<?php

namespace App\Imports;

use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\Region;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\DB;
use Throwable;

class HouseholdsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, SkipsEmptyRows, WithBatchInserts
{
    private array $errors = [];
    private int $successCount = 0;
    private int $failureCount = 0;

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        
        try {
            foreach ($rows as $index => $row) {
                $rowNum = $index + 2; // +2 for header row and 0-index
                
                // Find region by name or code
                $region = Region::where('name', $row['region'])
                    ->orWhere('code', $row['region'])
                    ->first();
                
                if (!$region) {
                    $this->errors[] = "Row {$rowNum}: Region '{$row['region']}' not found";
                    $this->failureCount++;
                    continue;
                }

                // Check for duplicate national ID
                if (Household::where('head_national_id', $row['national_id'])->exists()) {
                    $this->errors[] = "Row {$rowNum}: Household with National ID '{$row['national_id']}' already exists";
                    $this->failureCount++;
                    continue;
                }

                $spouseNationalId = $this->normalizeNationalId($row['spouse_national_id'] ?? null);
                if ($spouseNationalId && Household::where('spouse_national_id', $spouseNationalId)->exists()) {
                    $this->errors[] = "Row {$rowNum}: Spouse National ID '{$spouseNationalId}' already exists";
                    $this->failureCount++;
                    continue;
                }

                $spouseHasWarInjury = $this->normalizeBoolean($row['spouse_war_injury'] ?? null);
                $spouseHasChronicDisease = $this->normalizeBoolean($row['spouse_chronic_disease'] ?? null);
                $spouseHasDisability = $this->normalizeBoolean($row['spouse_disability'] ?? null);
                $spouseConditionType = trim((string) ($row['spouse_condition_type'] ?? ''));
                $spouseHasHealthFlag = $spouseHasWarInjury || $spouseHasChronicDisease || $spouseHasDisability;

                if ($spouseHasHealthFlag && $spouseConditionType === '') {
                    $this->errors[] = "Row {$rowNum}: spouse_condition_type is required when spouse health flags are selected";
                    $this->failureCount++;
                    continue;
                }

                $household = Household::create([
                    'head_national_id' => $row['national_id'],
                    'head_name' => $row['head_name'],
                    'head_birth_date' => $row['head_birth_date'] ?? null,
                    'spouse_full_name' => $row['spouse_full_name'] ?? null,
                    'spouse_national_id' => $spouseNationalId,
                    'spouse_birth_date' => $row['spouse_birth_date'] ?? null,
                    'spouse_has_war_injury' => $spouseHasWarInjury,
                    'spouse_has_chronic_disease' => $spouseHasChronicDisease,
                    'spouse_has_disability' => $spouseHasDisability,
                    'spouse_condition_type' => $spouseHasHealthFlag ? $spouseConditionType : null,
                    'spouse_health_notes' => $row['spouse_health_notes'] ?? null,
                    'region_id' => $region->id,
                    'address_text' => $row['address'] ?? null,
                    'housing_type' => $this->normalizeHousingType($row['housing_type'] ?? null),
                    'primary_phone' => $this->normalizePhone($row['phone'] ?? null),
                    'status' => 'pending',
                    'has_war_injury' => $this->normalizeBoolean($row['war_injury'] ?? null),
                    'has_chronic_disease' => $this->normalizeBoolean($row['chronic_disease'] ?? null),
                    'has_disability' => $this->normalizeBoolean($row['disability'] ?? null),
                    'condition_type' => $row['condition_type'] ?? null,
                    'condition_notes' => $row['condition_notes'] ?? null,
                ]);

                // Add members if provided (comma-separated in single column)
                if (!empty($row['member_names'])) {
                    $memberNames = explode(',', $row['member_names']);
                    $memberRelations = !empty($row['member_relations']) 
                        ? explode(',', $row['member_relations']) 
                        : [];
                    
                    foreach ($memberNames as $i => $name) {
                        $name = trim($name);
                        if (empty($name)) continue;
                        
                        HouseholdMember::create([
                            'household_id' => $household->id,
                            'full_name' => $name,
                            'relation_to_head' => trim($memberRelations[$i] ?? 'other'),
                        ]);
                    }
                }

                $this->successCount++;
            }
            
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'national_id' => ['required', 'digits:9'],
            'head_name' => ['required', 'string', 'max:255'],
            'head_birth_date' => ['required', 'date', 'before:today'],
            'spouse_full_name' => ['required', 'string', 'max:255'],
            'spouse_national_id' => ['required', 'digits:9'],
            'spouse_birth_date' => ['required', 'date', 'before:today'],
            'spouse_war_injury' => ['nullable'],
            'spouse_chronic_disease' => ['nullable'],
            'spouse_disability' => ['nullable'],
            'spouse_condition_type' => ['nullable', 'string', 'max:255'],
            'spouse_health_notes' => ['nullable', 'string', 'max:1000'],
            'region' => ['required', 'string'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'national_id.required' => 'National ID is required',
            'head_name.required' => 'Head name is required',
            'head_birth_date.required' => 'Head birth date is required',
            'spouse_full_name.required' => 'Spouse full name is required',
            'spouse_national_id.required' => 'Spouse national ID is required',
            'spouse_birth_date.required' => 'Spouse birth date is required',
            'spouse_condition_type.max' => 'Spouse condition type is too long',
            'region.required' => 'Region is required',
        ];
    }

    private function normalizeHousingType(?string $type): ?string
    {
        if (!$type) return null;
        
        $type = strtolower(trim($type));
        $map = [
            'owned' => 'owned',
            'own' => 'owned',
            'rented' => 'rented',
            'rent' => 'rented',
            'family' => 'family_hosted',
            'family hosted' => 'family_hosted',
            'hosted' => 'family_hosted',
            'other' => 'other',
        ];
        
        return $map[$type] ?? 'other';
    }

    private function normalizePhone($value): ?string
    {
        if (!$value) return null;
        $digits = preg_replace('/\\D/', '', (string) $value);
        return substr($digits, 0, 10);
    }

    private function normalizeNationalId($value): ?string
    {
        if (!$value) return null;
        $digits = preg_replace('/\\D/', '', (string) $value);
        return substr($digits, 0, 9);
    }

    /**
     * Normalize boolean values from import (accepts 0/1, yes/no, true/false, نعم/لا)
     */
    private function normalizeBoolean($value): bool
    {
        if ($value === null || $value === '') return false;
        
        $value = strtolower(trim((string) $value));
        $truthy = ['1', 'true', 'yes', 'y', 'نعم'];
        
        return in_array($value, $truthy, true);
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
        $this->failureCount++;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            $this->failureCount++;
        }
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailureCount(): int
    {
        return $this->failureCount;
    }
}
