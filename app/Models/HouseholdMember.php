<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'national_id',
        'full_name',
        'relation_to_head',
        'gender',
        'birth_date',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'gender' => 'string',
    ];

    /**
     * Get the household this member belongs to.
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Get the member's age.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }
        return $this->birth_date->age;
    }

    /**
     * Scope to search by national ID.
     */
    public function scopeByNationalId($query, string $nationalId)
    {
        return $query->where('national_id', $nationalId);
    }
}
