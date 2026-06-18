<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'name',
        'code',
        'opened_by',
        'status',
        'opening_amount',
        'closing_amount',
        'opened_at',
        'closed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'opening_amount' => 'decimal:2',
            'closing_amount' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function opener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function arrears(): HasMany
    {
        return $this->hasMany(CashArrear::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function getTotalInAttribute(): float
    {
        return (float) $this->movements()->where('type', 'in')->sum('amount');
    }

    public function getTotalOutAttribute(): float
    {
        return (float) $this->movements()->where('type', 'out')->sum('amount');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (CashRegister $register) {
            if (is_null($register->institution_id) && auth()->check()) {
                $register->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
