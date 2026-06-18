<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmergencyTriage extends Model
{
    use HasFactory;

    protected $table = 'emergency_triage';

    protected $fillable = [
        'institution_id',
        'patient_id',
        'doctor_id',
        'arrival_date',
        'chief_complaint',
        'triage_level',
        'triage_score',
        'assigned_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'arrival_date' => 'datetime',
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

    public function record(): HasOne
    {
        return $this->hasOne(EmergencyRecord::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByLevel($query, string $level)
    {
        return $query->where('triage_level', $level);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeCritical($query)
    {
        return $query->where('triage_level', 'I');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (EmergencyTriage $triage) {
            if (is_null($triage->institution_id) && auth()->check()) {
                $triage->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
