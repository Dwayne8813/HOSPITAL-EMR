<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'created_by',
        'name',
        'code',
        'type',
        'category',
        'content',
        'parameters',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'parameters' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function savedReports(): HasMany
    {
        return $this->hasMany(SavedReport::class, 'template_id');
    }

    public function scheduledReports(): HasMany
    {
        return $this->hasMany(ScheduledReport::class, 'template_id');
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
        return $query->where('type', $type);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ReportTemplate $template) {
            if (is_null($template->institution_id) && auth()->check()) {
                $template->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
