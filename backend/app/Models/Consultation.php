<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'consultation_type',
        'reason',
        'clinical_history',
        'physical_examination',
        'vital_signs_summary',
        'auxiliary_exams',
        'assessment',
        'diagnosis',
        'treatment_plan',
        'prescription_notes',
        'referral_notes',
        'follow_up_date',
        'duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            'duration_minutes' => 'integer',
            'physical_examination' => 'array',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
