<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_name',
        'logo_path',
        'signature_name',
        'signature_title',
        'footer_notes',
    ];

    /**
     * Get the singleton settings instance.
     */
    public static function getSettings(): self
    {
        return static::first() ?? static::create([
            'organization_name' => 'FamilyAid Organization',
        ]);
    }

    /**
     * Get the full logo path.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_path) {
            return null;
        }
        return asset('storage/' . $this->logo_path);
    }
}
