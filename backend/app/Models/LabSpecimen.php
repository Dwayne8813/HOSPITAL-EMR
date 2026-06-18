<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabSpecimen extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_order_id',
        'collected_by',
        'specimen_type',
        'collection_method',
        'collection_site',
        'collection_date',
        'received_at',
        'received_by',
        'status',
        'notes',
        'is_qc_passed',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'collection_date' => 'datetime',
            'received_at' => 'datetime',
            'is_qc_passed' => 'boolean',
        ];
    }

    public function labOrder(): BelongsTo
    {
        return $this->belongsTo(LabOrder::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
