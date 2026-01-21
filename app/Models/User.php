<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
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
        'national_id',
        'phone',
        'password',
        'household_id',
        'security_question',
        'security_answer_hash',
        'is_staff',
        'region_id',
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
        'is_staff' => 'boolean',
    ];

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
