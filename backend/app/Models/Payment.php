<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'institution_id',
        'received_by',
        'amount',
        'payment_method',
        'reference_number',
        'received_at',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'received_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function cashMovement(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CashMovement::class);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('received_at', '>=', now()->subDays($days));
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Payment $payment) {
            if (is_null($payment->institution_id) && auth()->check()) {
                $payment->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
