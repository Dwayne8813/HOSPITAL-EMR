<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatingRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'building_id',
        'floor_id',
        'room_id',
        'name',
        'number',
        'type',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function surgerySchedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SurgerySchedule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (OperatingRoom $operatingRoom) {
            if (is_null($operatingRoom->institution_id) && auth()->check()) {
                $operatingRoom->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
