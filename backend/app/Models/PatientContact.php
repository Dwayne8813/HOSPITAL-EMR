<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'name',
        'relationship',
        'phone',
        'email',
        'address',
        'is_emergency',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_emergency' => 'boolean',
            'is_primary' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
