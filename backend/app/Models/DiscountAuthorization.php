<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountAuthorization extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'authorized_by',
        'discount_percentage',
        'discount_amount',
        'reason',
        'status',
        'authorized_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'discount_percentage' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'authorized_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function authorizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
