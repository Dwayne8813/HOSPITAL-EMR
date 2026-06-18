<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Antecedent extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'personal_pathological',
        'personal_non_pathological',
        'family_pathological',
        'surgical',
        'obstetric',
        'gynecological',
        'allergies',
        'medications',
        'habits',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'personal_pathological' => 'array',
            'family_pathological' => 'array',
            'surgical' => 'array',
            'medications' => 'array',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
