<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrugInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_a_id',
        'medication_b_id',
        'severity',
        'description',
        'clinical_effect',
        'management',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function medicationA(): BelongsTo
    {
        return $this->belongsTo(Medication::class, 'medication_a_id');
    }

    public function medicationB(): BelongsTo
    {
        return $this->belongsTo(Medication::class, 'medication_b_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeSevere($query)
    {
        return $query->where('severity', 'severe');
    }

    public function scopeModerate($query)
    {
        return $query->where('severity', 'moderate');
    }

    public function scopeForMedication($query, int $medicationId)
    {
        return $query->where(function ($q) use ($medicationId) {
            $q->where('medication_a_id', $medicationId)
              ->orWhere('medication_b_id', $medicationId);
        });
    }
}
