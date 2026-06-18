<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'specialty_id',
        'created_by',
        'name',
        'description',
        'content',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ConsultationTemplate $template) {
            if (is_null($template->institution_id) && auth()->check()) {
                $template->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
