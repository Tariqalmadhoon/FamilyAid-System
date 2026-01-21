<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Household extends Model
{
    use HasFactory;

    protected $fillable = [
        'head_national_id',
        'head_name',
        'region_id',
        'address_text',
        'housing_type',
        'primary_phone',
        'secondary_phone',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
        'housing_type' => 'string',
    ];

    /**
     * Get the region of this household.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the user account for this household.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Get the members of this household.
     */
    public function members(): HasMany
    {
        return $this->hasMany(HouseholdMember::class);
    }

    /**
     * Get the distributions for this household.
     */
    public function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }

    /**
     * Get the latest distribution.
     */
    public function latestDistribution(): HasOne
    {
        return $this->hasOne(Distribution::class)->latestOfMany('distribution_date');
    }

    /**
     * Scope for verified households.
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope for pending households.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to search by phone.
     */
    public function scopeSearchByPhone($query, string $phone)
    {
        return $query->where(function ($q) use ($phone) {
            $q->where('primary_phone', 'like', "%{$phone}%")
              ->orWhere('secondary_phone', 'like', "%{$phone}%");
        });
    }

    /**
     * Get member count.
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }
}
