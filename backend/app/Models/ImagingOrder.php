<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ImagingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'medical_record_id',
        'patient_id',
        'doctor_id',
        'order_number',
        'imaging_type',
        'body_region',
        'clinical_reason',
        'clinical_history',
        'status',
        'priority',
        'notes',
        'ordered_at',
        'scheduled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
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

    public function result(): HasOne
    {
        return $this->hasOne(ImagingResult::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('imaging_type', $type);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ImagingOrder $order) {
            if (is_null($order->institution_id) && auth()->check()) {
                $order->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
