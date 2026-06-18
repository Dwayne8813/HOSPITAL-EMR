<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_order_item_id',
        'parameter',
        'parameter_name',
        'value',
        'unit',
        'reference_range',
        'reference_range_min',
        'reference_range_max',
        'is_abnormal',
        'flag',
        'method',
        'notes',
        'performed_at',
        'validated_by',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:4',
            'reference_range_min' => 'decimal:4',
            'reference_range_max' => 'decimal:4',
            'is_abnormal' => 'boolean',
            'performed_at' => 'datetime',
            'validated_at' => 'datetime',
        ];
    }

    public function labOrderItem(): BelongsTo
    {
        return $this->belongsTo(LabOrderItem::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function scopeAbnormal($query)
    {
        return $query->where('is_abnormal', true);
    }

    public function scopeHigh($query)
    {
        return $query->where('flag', 'high');
    }

    public function scopeLow($query)
    {
        return $query->where('flag', 'low');
    }

    public function isHigh(): bool
    {
        return $this->reference_range_max !== null && $this->value > $this->reference_range_max;
    }

    public function isLow(): bool
    {
        return $this->reference_range_min !== null && $this->value < $this->reference_range_min;
    }
}
