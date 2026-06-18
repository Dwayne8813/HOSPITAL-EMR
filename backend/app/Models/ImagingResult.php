<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagingResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'imaging_order_id',
        'findings',
        'impression',
        'recommendations',
        'file_path',
        'file_name',
        'file_type',
        'performed_by',
        'performed_at',
        'radiologist_id',
        'radiologist_notes',
        'radiologist_at',
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'datetime',
            'radiologist_at' => 'datetime',
        ];
    }

    public function imagingOrder(): BelongsTo
    {
        return $this->belongsTo(ImagingOrder::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function radiologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'radiologist_id');
    }
}
