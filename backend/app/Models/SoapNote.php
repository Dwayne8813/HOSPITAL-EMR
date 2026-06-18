<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoapNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'subjective',
        'objective',
        'assessment',
        'plan',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subjective' => 'array',
            'objective' => 'array',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
