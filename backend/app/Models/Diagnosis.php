<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'cie10_code_id',
        'diagnosis_type',
        'description',
        'is_principal',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_principal' => 'boolean',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function cie10Code(): BelongsTo
    {
        return $this->belongsTo(Cie10Code::class);
    }

    public function scopePrincipal($query)
    {
        return $query->where('is_principal', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('diagnosis_type', $type);
    }
}
