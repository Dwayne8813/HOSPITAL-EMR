<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'patient_id',
        'from_doctor_id',
        'to_doctor_id',
        'to_institution_name',
        'to_specialty',
        'reason',
        'clinical_summary',
        'current_medications',
        'allergies',
        'vital_signs',
        'diagnosis',
        'urgency',
        'status',
        'sent_at',
        'received_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'received_at' => 'datetime',
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

    public function fromDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_doctor_id');
    }

    public function toDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_doctor_id');
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function scopeUrgent($query)
    {
        return $query->where('urgency', 'urgent');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ReferralLetter $letter) {
            if (is_null($letter->institution_id) && auth()->check()) {
                $letter->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
