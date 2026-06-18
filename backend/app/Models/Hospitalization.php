<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospitalization extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'patient_id',
        'doctor_id',
        'bed_id',
        'room_id',
        'admission_number',
        'type',
        'status',
        'reason',
        'admission_date',
        'expected_discharge_date',
        'discharged_at',
        'discharge_type',
        'discharge_notes',
        'diagnosis_at_admission',
        'diagnosis_at_discharge',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'admission_date' => 'datetime',
            'expected_discharge_date' => 'date',
            'discharged_at' => 'datetime',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function evolutions(): HasMany
    {
        return $this->hasMany(HospitalizationEvolution::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(BedTransfer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->whereNull('discharged_at');
    }

    public function scopeDischarged($query)
    {
        return $query->where('status', 'discharged')->whereNotNull('discharged_at');
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeByPatient($query, int $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByDoctor($query, int $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function getDurationDaysAttribute(): ?int
    {
        $end = $this->discharged_at ?? now();
        return $this->admission_date?->diffInDays($end);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Hospitalization $hospitalization) {
            if (is_null($hospitalization->institution_id) && auth()->check()) {
                $hospitalization->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
