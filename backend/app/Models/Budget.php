<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'cost_center_id',
        'name',
        'year',
        'month',
        'amount',
        'spent',
        'remaining',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'amount' => 'decimal:2',
            'spent' => 'decimal:2',
            'remaining' => 'decimal:2',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeByYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByCostCenter($query, int $costCenterId)
    {
        return $query->where('cost_center_id', $costCenterId);
    }

    public function getPercentageUsedAttribute(): float
    {
        if ($this->amount <= 0) return 0;
        return round(($this->spent / $this->amount) * 100, 2);
    }

    public function isOverBudget(): bool
    {
        return $this->spent > $this->amount;
    }
}
