<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'aid_program_id',
        'distributed_by',
        'distribution_date',
        'notes',
    ];

    protected $casts = [
        'distribution_date' => 'date',
    ];

    /**
     * Get the household that received this distribution.
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Get the aid program for this distribution.
     */
    public function aidProgram(): BelongsTo
    {
        return $this->belongsTo(AidProgram::class);
    }

    /**
     * Get the user who recorded this distribution.
     */
    public function distributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    /**
     * Scope by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('distribution_date', [$from, $to]);
    }

    /**
     * Scope by program.
     */
    public function scopeForProgram($query, $programId)
    {
        return $query->where('aid_program_id', $programId);
    }
}
