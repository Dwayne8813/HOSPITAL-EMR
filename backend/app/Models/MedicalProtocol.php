<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalProtocol extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'specialty_id',
        'name',
        'code',
        'description',
        'content',
        'category',
        'version',
        'effective_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeBySpecialty($query, int $specialtyId)
    {
        return $query->where('specialty_id', $specialtyId);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (MedicalProtocol $protocol) {
            if (is_null($protocol->institution_id) && auth()->check()) {
                $protocol->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
