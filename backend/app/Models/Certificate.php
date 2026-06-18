<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'medical_record_id',
        'patient_id',
        'doctor_id',
        'certificate_number',
        'type',
        'content',
        'start_date',
        'end_date',
        'days_off',
        'restrictions',
        'issued_at',
        'signed_at',
        'is_digitally_signed',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'days_off' => 'integer',
            'issued_at' => 'datetime',
            'signed_at' => 'datetime',
            'is_digitally_signed' => 'boolean',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeByPatient($query, int $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Certificate $certificate) {
            if (is_null($certificate->institution_id) && auth()->check()) {
                $certificate->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
