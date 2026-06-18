<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Communication extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'sender_id',
        'recipient_id',
        'subject',
        'message',
        'type',
        'priority',
        'is_read',
        'read_at',
        'parent_id',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'attachments' => 'array',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeBySender($query, int $senderId)
    {
        return $query->where('sender_id', $senderId);
    }

    public function scopeByRecipient($query, int $recipientId)
    {
        return $query->where('recipient_id', $recipientId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeInbox($query, int $userId)
    {
        return $query->where('recipient_id', $userId);
    }

    public function scopeSent($query, int $userId)
    {
        return $query->where('sender_id', $userId);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Communication $communication) {
            if (is_null($communication->institution_id) && auth()->check()) {
                $communication->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
