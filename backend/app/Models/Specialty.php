<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(User::class)->whereHas('role', fn ($q) => $q->where('slug', 'doctor'));
    }

    public function medicalProtocols(): HasMany
    {
        return $this->hasMany(MedicalProtocol::class);
    }

    public function consultationTemplates(): HasMany
    {
        return $this->hasMany(ConsultationTemplate::class);
    }

    public function clinicalPathways(): HasMany
    {
        return $this->hasMany(ClinicalPathway::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }
}
