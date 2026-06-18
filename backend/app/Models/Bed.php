<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'institution_id',
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

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function hospitalization(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Hospitalization::class)->whereNull('discharged_at');
    }

    public function transfersFrom(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BedTransfer::class, 'from_bed_id');
    }

    public function transfersTo(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BedTransfer::class, 'to_bed_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    public function scopeByRoom($query, int $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Bed $bed) {
            if (is_null($bed->institution_id) && auth()->check()) {
                $bed->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
