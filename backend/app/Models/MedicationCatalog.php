<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicationCatalog extends Model
{
    use HasFactory;

    protected $table = 'medication_catalog';

    protected $fillable = [
        'name',
        'generic_name',
        'description',
        'active_ingredient',
        'concentration',
        'pharmaceutical_form',
        'manufacturer',
        'country_of_origin',
        'requires_prescription',
        'is_controlled',
        'controlled_level',
        'contraindications',
        'side_effects',
        'storage_conditions',
    ];

    protected function casts(): array
    {
        return [
            'requires_prescription' => 'boolean',
            'is_controlled' => 'boolean',
        ];
    }

    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class, 'catalog_id');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('generic_name', 'LIKE', "%{$search}%")
              ->orWhere('active_ingredient', 'LIKE', "%{$search}%");
        });
    }

    public function scopePrescriptionRequired($query)
    {
        return $query->where('requires_prescription', true);
    }

    public function scopeControlled($query)
    {
        return $query->where('is_controlled', true);
    }
}
