<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'catalog_id',
        'category_id',
        'name',
        'generic_name',
        'concentration',
        'pharmaceutical_form',
        'manufacturer',
        'requires_prescription',
        'is_controlled',
        'min_stock',
        'current_stock',
        'unit_price',
        'cost_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requires_prescription' => 'boolean',
            'is_controlled' => 'boolean',
            'min_stock' => 'integer',
            'current_stock' => 'integer',
            'unit_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(MedicationCatalog::class, 'catalog_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MedicationCategory::class, 'category_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(MedicationBatch::class);
    }

    public function dispensations(): HasMany
    {
        return $this->hasMany(MedicationDispensation::class);
    }

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('generic_name', 'LIKE', "%{$search}%");
        });
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Medication $medication) {
            if (is_null($medication->institution_id) && auth()->check()) {
                $medication->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
