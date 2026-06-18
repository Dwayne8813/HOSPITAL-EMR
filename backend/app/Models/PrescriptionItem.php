<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'medication_name',
        'medication_id',
        'dosage',
        'dosage_unit',
        'frequency',
        'route',
        'duration',
        'duration_days',
        'quantity',
        'refill_quantity',
        'instructions',
        'is_prn',
        'is_compound',
        'compound_recipe',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'dosage' => 'decimal:2',
            'duration_days' => 'integer',
            'quantity' => 'integer',
            'refill_quantity' => 'integer',
            'is_prn' => 'boolean',
            'is_compound' => 'boolean',
            'compound_recipe' => 'array',
        ];
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    public function dispensations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MedicationDispensation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
