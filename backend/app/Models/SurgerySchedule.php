<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SurgerySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'patient_id',
        'doctor_id',
        'operating_room_id',
        'surgery_type',
        'description',
        'status',
        'priority',
        'scheduled_start',
        'scheduled_end',
        'actual_start',
        'actual_end',
        'anesthesia_type',
        'notes',
        'pre_op_notes',
        'post_op_notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_start' => 'datetime',
            'scheduled_end' => 'datetime',
            'actual_start' => 'datetime',
            'actual_end' => 'datetime',
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

    public function operatingRoom(): BelongsTo
    {
        return $this->belongsTo(OperatingRoom::class);
    }

    public function team(): HasMany
    {
        return $this->hasMany(SurgeryTeam::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(SurgeryMaterial::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(SurgeryReport::class);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeByDate($query, string $date)
    {
        return $query->whereDate('scheduled_start', $date);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_start', today());
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (SurgerySchedule $surgery) {
            if (is_null($surgery->institution_id) && auth()->check()) {
                $surgery->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
