<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;

    public const ALLOWED_CAMP_REGION_NAMES = [
        'مخيم الابرار(الغفران)',
        'مخيم الامام مالك بن انس',
        'مخيم ام القرى',
        'مخيم عثمان بن عفان',
        'مخيم الايمان',
    ];

    protected $fillable = [
        'name',
        'parent_id',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent region.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    /**
     * Get the child regions.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Region::class, 'parent_id');
    }

    /**
     * Get the households in this region.
     */
    public function households(): HasMany
    {
        return $this->hasMany(Household::class);
    }

    /**
     * Get all descendants recursively.
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Scope for active regions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for allowed camp regions shown in citizen/admin selects.
     */
    public function scopeAllowedCamps($query)
    {
        return $query->active()->whereIn('name', self::ALLOWED_CAMP_REGION_NAMES);
    }
}
