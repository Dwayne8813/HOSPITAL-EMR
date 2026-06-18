<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriageProtocol extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'name',
        'level',
        'criteria',
        'color_code',
        'max_wait_minutes',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'criteria' => 'array',
            'max_wait_minutes' => 'integer',
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

    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }
}
