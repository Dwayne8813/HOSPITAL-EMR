<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'inventory_item_id',
        'medication_id',
        'performed_by',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'notes',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'performed_at' => 'datetime',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('performed_at', '>=', now()->subDays($days));
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (InventoryMovement $movement) {
            if (is_null($movement->institution_id) && auth()->check()) {
                $movement->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
