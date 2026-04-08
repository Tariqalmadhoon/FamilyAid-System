<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'father_name',
        'grandfather_name',
        'last_name',
        'birth_date',
        'national_id',
        'phone',
        'password',
        'household_id',
        'security_question',
        'security_answer_hash',
        'is_staff',
        'region_id',
        'camp_permissions_configured',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'security_answer_hash',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
        'is_staff' => 'boolean',
        'camp_permissions_configured' => 'boolean',
    ];

    /**
     * Accessor to build a full name from structured parts (fallbacks to legacy name).
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->father_name,
            $this->grandfather_name,
            $this->last_name,
        ], fn ($value) => filled($value));

        if (count($parts) === 0) {
            return (string) $this->name;
        }

        return trim(collect($parts)->join(' '));
    }

    /**
     * Get the household for this citizen user.
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Get the region for this staff user.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get distributions this user has recorded.
     */
    public function distributionsRecorded(): HasMany
    {
        return $this->hasMany(Distribution::class, 'distributed_by');
    }

    /**
     * Get audit logs for this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_user_id');
    }

    /**
     * Get import jobs created by this user.
     */
    public function importJobs(): HasMany
    {
        return $this->hasMany(ImportJob::class);
    }

    /**
     * Check if user is a citizen (has household).
     */
    public function isCitizen(): bool
    {
        return !$this->is_staff && $this->household_id !== null;
    }

    /**
     * Check if user is staff.
     */
    public function isStaff(): bool
    {
        return $this->is_staff;
    }

    /**
     * Check if user is a camp manager.
     */
    public function isCampManager(): bool
    {
        return $this->hasRole('camp_manager');
    }

    /**
     * Get the managed camp region id for a camp manager account.
     */
    public function managedRegionId(): ?int
    {
        if (! $this->isCampManager()) {
            return null;
        }

        return $this->region_id ? (int) $this->region_id : null;
    }

    /**
     * Check whether the user can access a specific camp region.
     */
    public function canAccessRegion(?int $regionId): bool
    {
        if (! $this->isCampManager()) {
            return true;
        }

        return $this->managedRegionId() !== null
            && $regionId !== null
            && $this->managedRegionId() === (int) $regionId;
    }

    /**
     * Permissions that can be configured for camp managers from the UI.
     */
    public static function configurableCampManagerPermissions(): array
    {
        return [
            'households.view',
            'households.create',
            'households.update',
            'households.delete',
            'households.verify',
            'households.import',
            'households.export',
            'members.view',
            'members.create',
            'members.update',
            'members.delete',
            'distributions.view',
            'distributions.create',
            'distributions.delete',
            'distributions.export',
        ];
    }

    /**
     * Whether this camp manager has an explicit permission profile configured by super admin.
     */
    public function usesConfiguredCampPermissions(): bool
    {
        return $this->isCampManager() && (bool) $this->camp_permissions_configured;
    }

    /**
     * Check application permission with camp-manager overrides.
     */
    public function hasManagementPermission(string $permission): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        if ($this->isCampManager()) {
            if ($this->usesConfiguredCampPermissions()) {
                return $this->hasDirectPermission($permission);
            }

            return $this->hasPermissionTo($permission);
        }

        return $this->hasPermissionTo($permission);
    }

    /**
     * Check whether the user has any of the provided permissions.
     */
    public function hasAnyManagementPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasManagementPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the effective camp-manager permissions for display in the UI.
     */
    public function effectiveCampManagerPermissions(): Collection
    {
        $availablePermissions = collect(self::configurableCampManagerPermissions());

        if (! $this->isCampManager()) {
            return collect();
        }

        if ($this->usesConfiguredCampPermissions()) {
            return $this->getDirectPermissions()
                ->pluck('name')
                ->intersect($availablePermissions)
                ->values();
        }

        return $availablePermissions
            ->filter(fn (string $permission) => $this->hasPermissionTo($permission))
            ->values();
    }

    /**
     * Verify security answer.
     */
    public function verifySecurityAnswer(string $answer): bool
    {
        if (!$this->security_answer_hash) {
            return false;
        }
        return Hash::check(strtolower(trim($answer)), $this->security_answer_hash);
    }

    /**
     * Set the security answer (automatically hashes).
     */
    public function setSecurityAnswer(string $answer): void
    {
        $this->security_answer_hash = Hash::make(strtolower(trim($answer)));
    }

    /**
     * Scope for staff users.
     */
    public function scopeStaff($query)
    {
        return $query->where('is_staff', true);
    }

    /**
     * Scope for citizen users.
     */
    public function scopeCitizens($query)
    {
        return $query->where('is_staff', false)->whereNotNull('household_id');
    }
}
