<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
        'head_birth_date',
        'citizen_head_name_updated_at',
        'spouse_full_name',
        'spouse_national_id',
        'spouse_birth_date',
        'spouse_has_war_injury',
        'spouse_has_chronic_disease',
        'spouse_has_disability',
        'spouse_condition_type',
        'spouse_health_notes',
        'region_id',
        'address_text',
        'housing_type',
        'primary_phone',
        'secondary_phone',
        'status',
        'notes',
        'has_war_injury',
        'has_chronic_disease',
        'has_disability',
        'condition_type',
        'condition_notes',
        'previous_governorate',
        'previous_area',
        'payment_account_type',
        'payment_account_number',
        'payment_account_holder_name',
    ];

    protected $casts = [
        'status' => 'string',
        'housing_type' => 'string',
        'head_birth_date' => 'date',
        'citizen_head_name_updated_at' => 'datetime',
        'has_war_injury' => 'boolean',
        'has_chronic_disease' => 'boolean',
        'has_disability' => 'boolean',
        'spouse_birth_date' => 'date',
        'spouse_has_war_injury' => 'boolean',
        'spouse_has_chronic_disease' => 'boolean',
        'spouse_has_disability' => 'boolean',
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
     * Scope households visible to the given user.
     */
    public function scopeVisibleTo(Builder $query, ?User $user = null): Builder
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! $user->isCampManager()) {
            return $query;
        }

        $managedRegionId = $user->managedRegionId();

        if ($managedRegionId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('region_id', $managedRegionId);
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

    /**
     * Scope for households with children under a certain age (in months).
     */
    public function scopeHasChildUnderMonths($query, int $months = 24)
    {
        $cutoffDate = now()->subMonths($months)->toDateString();
        
        return $query->whereExists(function ($subquery) use ($cutoffDate) {
            $subquery->selectRaw('1')
                ->from('household_members')
                ->whereColumn('household_members.household_id', 'households.id')
                ->where('household_members.birth_date', '>=', $cutoffDate)
                ->whereNotNull('household_members.birth_date');
        });
    }

    /**
     * Scope for war injury filter.
     */
    public function scopeHasWarInjury($query)
    {
        return $query->where('has_war_injury', true);
    }

    /**
     * Scope for chronic disease filter.
     */
    public function scopeHasChronicDisease($query)
    {
        return $query->where('has_chronic_disease', true);
    }

    /**
     * Scope for disability filter.
     */
    public function scopeHasDisability($query)
    {
        return $query->where('has_disability', true);
    }

    /**
     * Check if household has any health condition.
     */
    public function hasAnyHealthCondition(): bool
    {
        return $this->has_war_injury || $this->has_chronic_disease || $this->has_disability;
    }

    /**
     * Check whether the citizen can update the household head name now.
     */
    public function canCitizenUpdateHeadNameAt(CarbonInterface|string|null $moment = null): bool
    {
        if (!$this->citizen_head_name_updated_at) {
            return true;
        }

        $moment = $moment ? Carbon::parse($moment) : now();

        return $this->citizen_head_name_updated_at->format('Y-m') !== $moment->format('Y-m');
    }

    /**
     * Get the next time when a citizen self-update becomes available again.
     */
    public function nextCitizenHeadNameUpdateAt(): ?CarbonInterface
    {
        if ($this->canCitizenUpdateHeadNameAt()) {
            return null;
        }

        return $this->citizen_head_name_updated_at
            ? $this->citizen_head_name_updated_at->copy()->startOfMonth()->addMonth()
            : null;
    }
}
