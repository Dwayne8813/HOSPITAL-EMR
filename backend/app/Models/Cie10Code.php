<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cie10Code extends Model
{
    use HasFactory;

    protected $table = 'cie10_codes';

    protected $fillable = [
        'category_id',
        'code',
        'description',
        'gender',
        'age_group',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Cie10Category::class, 'category_id');
    }

    public function diagnoses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('code', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }
}
