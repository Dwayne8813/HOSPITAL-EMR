<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientSymptom extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'patient_id',
        'symptom',
        'description',
        'severity',
        'duration',
        'onset',
        'location',
        'quality',
        'context',
        'associated_symptoms',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'associated_symptoms' => 'array',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByMedicalRecord($query, int $medicalRecordId)
    {
        return $query->where('medical_record_id', $medicalRecordId);
    }
}
