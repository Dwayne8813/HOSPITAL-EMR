<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelemedicineSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'medical_record_id',
        'patient_id',
        'doctor_id',
        'session_number',
        'status',
        'type',
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration_minutes',
        'room_id',
        'recording_url',
        'notes',
        'technical_issues',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'duration_minutes' => 'integer',
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

    public function messages(): HasMany
    {
        return $this->hasMany(TelemedicineMessage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (TelemedicineSession $session) {
            if (is_null($session->institution_id) && auth()->check()) {
                $session->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
