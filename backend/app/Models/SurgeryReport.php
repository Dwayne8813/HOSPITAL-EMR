<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurgeryReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'surgery_schedule_id',
        'performed_by',
        'report_content',
        'complications',
        'blood_loss_ml',
        'duration_minutes',
        'anesthesia_notes',
        'findings',
        'specimens_sent',
        'post_op_instructions',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'blood_loss_ml' => 'decimal:1',
            'duration_minutes' => 'integer',
            'signed_at' => 'datetime',
        ];
    }

    public function surgerySchedule(): BelongsTo
    {
        return $this->belongsTo(SurgerySchedule::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
