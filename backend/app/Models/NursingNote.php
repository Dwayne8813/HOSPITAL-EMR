<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NursingNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'patient_id',
        'nurse_id',
        'note_type',
        'content',
        'vital_signs',
        'interventions',
        'patient_response',
        'notes',
        'noted_at',
    ];

    protected function casts(): array
    {
        return [
            'vital_signs' => 'array',
            'interventions' => 'array',
            'noted_at' => 'datetime',
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

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('note_type', $type);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('noted_at', '>=', now()->subDays($days));
    }
}
