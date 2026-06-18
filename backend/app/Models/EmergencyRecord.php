<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'emergency_triage_id',
        'initial_assessment',
        'diagnosis',
        'treatment_given',
        'medications_administered',
        'procedures_performed',
        'disposition',
        'disposition_notes',
        'follow_up_required',
        'follow_up_date',
        'referred_to',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'medications_administered' => 'array',
            'procedures_performed' => 'array',
            'follow_up_required' => 'boolean',
            'follow_up_date' => 'date',
        ];
    }

    public function triage(): BelongsTo
    {
        return $this->belongsTo(EmergencyTriage::class, 'emergency_triage_id');
    }
}
