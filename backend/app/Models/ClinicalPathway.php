<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalPathway extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'specialty_id',
        'name',
        'code',
        'description',
        'diagnosis',
        'steps',
        'expected_duration_days',
        'version',
        'is_active',
        'effective_date',
    ];

    protected function casts(): array
    {
        return [
            'steps' => 'array',
            'expected_duration_days' => 'integer',
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeBySpecialty($query, int $specialtyId)
    {
        return $query->where('specialty_id', $specialtyId);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ClinicalPathway $pathway) {
            if (is_null($pathway->institution_id) && auth()->check()) {
                $pathway->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
