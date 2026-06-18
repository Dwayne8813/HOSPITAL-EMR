<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'medical_record_id',
        'patient_id',
        'doctor_id',
        'prescription_number',
        'prescription_date',
        'status',
        'notes',
        'is_compound',
        'refill_count',
        'refill_interval_days',
        'prescribed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_compound' => 'boolean',
            'refill_count' => 'integer',
            'refill_interval_days' => 'integer',
            'prescribed_at' => 'datetime',
            'expires_at' => 'datetime',
            'prescription_date' => 'date',
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

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeByPatient($query, int $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Prescription $prescription) {
            if (is_null($prescription->institution_id) && auth()->check()) {
                $prescription->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
