<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurgeryTeam extends Model
{
    use HasFactory;

    protected $table = 'surgery_team';

    protected $fillable = [
        'surgery_schedule_id',
        'user_id',
        'role',
        'notes',
    ];

    public function surgerySchedule(): BelongsTo
    {
        return $this->belongsTo(SurgerySchedule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
