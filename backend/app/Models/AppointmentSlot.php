<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'duration_minutes',
        'is_available',
        'slot_type',
        'max_appointments',
        'current_appointments',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'is_available' => 'boolean',
            'max_appointments' => 'integer',
            'current_appointments' => 'integer',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                     ->whereColumn('current_appointments', '<', 'max_appointments');
    }

    public function scopeByDoctor($query, int $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeByDay($query, string $day)
    {
        return $query->where('day_of_week', $day);
    }

    public function isFullyBooked(): bool
    {
        return $this->current_appointments >= $this->max_appointments;
    }
}
