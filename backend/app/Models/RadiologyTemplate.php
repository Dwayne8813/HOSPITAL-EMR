<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RadiologyTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'name',
        'imaging_type',
        'body_region',
        'template_content',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'template_content' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('imaging_type', $type);
    }
}
